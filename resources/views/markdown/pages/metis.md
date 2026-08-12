---
title: Metis
published_at: 2026-08-12
summary: Our new centralized knowledge system
---

# Metis

Like, I'm sure, many many people right now, I'm navigating the pros and cons of integrating 
LLMs into more processes at work. We use Claude Code daily for development at InterNACHI, and
I've started to rely on it inside a personal [Obsidian](https://obsidian.md/) vault to manage
meeting notes/etc.

That said, I'm still pretty nervous about going all-in on a service that just wants to ingest
all your data and make it available via some sort of natural language interface. I would love
to be able to synthesize a conversation that was spread across two Slack channels, a Zoom call,
and maybe a [Tuple](https://tuple.app/) session (and at this point our team has accepted the
trade-offs of ever-present transcriptions), but I don't necessarily want to trust or get locked 
in to a specific vendor.

So, like so many developers in 2026, **I decided to just build my own thing**.

## Introducing Metis: a centralized knowledge system you own

Metis is a Laravel application that ships with a number of _Connectors_ that let you ingest your
data into a postgres database with pgvector indexes. Everything ingested is _tagged_ with the people
or groups that own the data, and only those people can retrieve that data later. Metis exposes an
MCP server, a CLI, and a Slack bot for querying data.

When building Metis, I set a few hard rules:

1. Only people who saw the original content (were in the Slack channel, present during the Zoom call, etc)
   can access that data via Metis. This is a hard boundary that happens before any LLM processing of
   requests, so it can't be manipulated via prompt injection.
2. Everyone must consent to their data being ingested into Metis (some tools, like Zoom, show a consent 
   dialog before someone can join a call with transcription on; others require the person importing the
   data to confirm that consent was obtained; etc).
3. The system must require minimal tending (a perfect system that requires constant supervision is worse
   than a good enough system that requires no oversight).

With those rules in place, I whipped up an MVP and deployed to Laravel Cloud.

## How it works today

### Permissions

Before we can talk about how data is ingested, we need to talk about how permissions work in Metis. We chose
a tagging system that allows us to handle permissions pretty flexibly but still adhere to the strict privacy
rules I set at the very beginning.

Basically, every piece of data can be _tagged_ with any number of strings. These might look like 
`user:demo@localhost` or `group:dev-team` or `org:internachi`. Each person inside of Metis is also tagged with
the tags they have access to. So the `demo@localhost` user would have the `user:demo@localhost` tag. And if they're
part of the InterNACHI organization, they'll also have the `org:internachi` tag. When querying for data, we 
always restrict queries to data that intersect with the active user's tags (something like `where tags && :person_tags`)
which ensures that all data a user can query is data they have permission to see.

(We enforce this thru a global scope attached to each model, architecture tests that ensure that scope isn't
removed except for during ingestion, and custom postgres functions that guard at the database level.)

### Ingestion

The basic flow of Metis right now is:

- Data comes in via webhooks or manual ingestion
- The first step is a consent gate that confirms that everyone has given consent for the data to be processed
- The data runs thru a pipeline that:
  - resolves all participants into tags (more on that later)
  - distills the transcript into smaller chunks (decisions, action items, facts, etc)
  - generates embeddings for each chunk
  - marks the data "ready" for retrieval

The piece that is most interesting here is how Metis handles participants.

Every time data is ingested, a list of participants is included with the transcript. For Slack, that might
look like a list of IDs like `UABC12345`. In Zoom, it's either a numeric ID or an email address depending on
the particular API. In Tuple, it's both. For each of these external IDs we create a unique tag that looks
something like `person:unresolved-96e67ce…` that's just a hash of the data source (like `"slack"`) and their
external ID. We _also_ check a lookup table and match that external ID to a known person in the system. If there's
a match, we also apply that person's tag to the content (so maybe `user:demo@localhost`). If not, we add that
external ID to an "unresolved people" list that an admin can manually review. When an unresolved person is matched,
we kick off a job to find every piece of content with `person:unresolved-96e67ce…` and tag it with the appropriate
user. This means that everyone already in the system gets access to the data immediately, and everyone added to
the system later gets access to their own data retroactively.

### Retrieval

The easiest part of this system is retrieval. We use the [Laravel AI toolkit](https://laravel.com/ai) to expose
an MCP server with a few tool calls: `Answer` for answering a question, `Facts` for looking up established knowledge,
`Search` for more general queries, and `GetEpisode` for loading all the data related to a particular event (a specific
call or Slack thread, for example). The AI SDK makes this essentially free, and was done in about 5 minutes with a
single prompt.

## Deploying Metis

(Eventually Metis will be open source. We're validating it internally first.)

Because I've been working on the same codebase for nearly 30 years, I rarely get to enjoy a greenfield
project. The last time I built a brand-new side project was a few years ago, and going from zero to deployed wasn't
the simplest process. There were a bunch of decisions to make about the stack ahead of time, and then I had to 
provision servers and set up supervisor and cron and Redis and SQS and a database (and on and on and on).

This time around, a quick `laravel new` + [Laravel Cloud](https://laravel.com/cloud) made the whole process a
dream. We decided to stick with technology that Cloud supports (like postgres and pgvector over something like
turbopuffer or graphiti and neo4j) and so far that hasn't been a problem at all. A few checkboxes in the Cloud
interface got me everything I need for production workloads, and scale to zero means that while we're experimenting
with Metis we pay nothing if no one is using it.

So far, I can't imagine ever deploying another Laravel app anywhere else.

## A few random thoughts

The Cerebras "[how we built our knowledge base](https://www.cerebras.ai/blog/how-we-built-our-knowledge-base)" article
was an instrumental point of reference when building Metis, especially around the decision to *always distill transcripts*
rather than storing the full raw transcript for retrieval. So far, I _think_ this is the right call, but it's something
that we're evaluating.

We're also already starting to feel some of the pain of the more restrictive permission system. I think that we'll probably
have to introduce new mechanisms that promote episodes from "only these people have access" to "anyone in these groups have access."
Right now I'm trying to hold back on anything that weakens the core privacy guarantees, but I think we'll eventually revisit 
and look into ways to let a group choose to share a transcript with others.

If you're wondering about the name "Metis" — it's named after the Greek mythological titan who was associated with wisdom, 
deep thought, and counsel.
