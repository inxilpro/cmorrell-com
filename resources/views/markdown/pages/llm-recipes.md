---
date: 2025-08-20
---

# LLM Recipes

There are a lot of things that LLMs are still **not good** for (making decisions is sure a big one). But I'm finding more and more use-cases that are helpful, and I've decided to document some of them here.

## Recipe: Travel Profile

I was recently working with a travel agent (_believe it or not, travel agents are a great, relatively affordable option in 2025_) and was having trouble articulating exactly what I wanted. After a while of getting nowhere, I turned to Claude and asked:

> I would like to define a travel profile that summarizes my hotel, restaurant, and activity preferences.
> Please ask me clarifying questions to establish the profile. Here are a few things to get started:
> 
> - [list of items]

From this, Claude asked a number of clarifying questions and then gave me a 2-3 paragraph summary to share with my travel agent. This was very helpful as it took:

> I don't like these certain hotels, but I can't explain why

And turned it into:

> I'm seeking independent, design-forward boutique hotels (over corporate chains)

And it asked me some questions about this:

> I'm a foodie, but don't love uncooked meats, or organ meats

And helped me write this instead:

> I'm a foodie who gravitates toward chef-driven restaurants that feel approachable rather than stuffy—no suit-required fine dining or lengthy prix fixe experiences

Now I have a document that I can use to help guide this and future trips. It's incredibly useful.

## Recipe: Doctor Appointment Prep

Just a few days ago I had a little health scare that meant I needed to visit a cardiologist (everything seems OK, and apparently I have a “beautiful heart”). I'm not particularly good with doctors, and I tend to have a hard time remembering all the relevant information in the moment, so I was nervous that I'd miss an important detail during my visit.

So I decided to write up my symptoms with the help of ChatGPT:

> I have an appointment with a cardiologist for intermittent pain in my chest. Here's a brief summary of what I'm experiencing:
> 
> - [list of items]
> 
> Please ask clarifying questions—especially ones that are likely to be asked by my doctor—and help me write a summary in a format that will be most helpful for them during the appointment.

After 5-10 mins of back-and-forth, I had a basic document that I was able to refine and print for my visit. My doctor was blown away, and said more than once that the summary helped take some of the time-pressure off of the appointment, since we spent so much less time just answering the question "how would you describe what's happening?"

## Recipe: Getting Un-Stuck

I recently [spoke at Laracon US](https://youtu.be/qLC04_BPQTY?si=mgwi5MkBpVX4w85v) and during my talk prep I got stuck: I had a solid intro, and knew what demos I wanted to walk though, but I was struggling with some transitions and the ending. Rather than working on refining what I had, I found myself stuck on what was missing. And no matter how many times I sat down to work on my talk, I couldn't get past this mental block.

So after a week or two of going nowhere, I tried something different: I grabbed my headphones, openend up ChatGPT's voice conversation mode, and as I walked along the [beautiful Schuylkill River Trail](https://www.schuylkillbanks.org/) I started a conversation:

> I'm working on a talk for Laracon US, and I'm stuck. I just want to run through a bunch of ideas. Ask occasional questions where appropriate.

After 45 minutes of wandering, I asked ChatGPT to summarize what I'd rambled about—taking particular care to reassemble all the pieces that I'd delivered out-of-order in the order I'd eventually landed on (accounting for lots ot "oh, actually, before that I want to mention…").

The summary was enough to get me started again, and there were even a few useful suggestions in there that helped inspire how I eventually structured the transitions between demos.

## Current takeaways

There's a lot of discussion about whether LLMs are bad, or whether you're a fool for not 100x-ing your development workflow by going all-in on multiple parallel Claude Code instances. But the biggest take-away for me around “AI” of late is that these are **useful tools**.

They're tools that we still need to intelligently reach for when they're the right tool for the job, and we can develop competency with over time.

When I use “AI” as a tool to augment what I'm good at, or help me navigate what I struggle with, it tends to go well. So far, when I've tried to use “AI” to _do things for me_, it's been _much less_ successful. Maybe that will change over time, but that's my reality today.

So while the big debates about whether “AI” will revolutionize everything or prove to be overhyped have their place, I also think there's real value in the practical work of figuring out what actually helps us today. I'm going to continue to do that, and might occasionally update this page with what I discover. And if the ratio of “look at this practical use of Claude” posts to “how I used AI to build the next unicorn” posts that I see on social media was a little more skewed towards the former than it currently is, I wouldn't mind… 
