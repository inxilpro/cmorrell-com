---
title: Exponential Versioning (ExpVer) - Chris Morrell
---

# Exponential Versioning 0.0.256

## Summary

Given a version number ANCHOR.VIBES.EXPONENTIAL, increment the:

1. ANCHOR version NEVER,
2. VIBES version when you feel like it, and
3. EXPONENTIAL version by raising it to the power of itself for every release.

## Introduction

In the world of software management there exists a dreaded place called
"version number ennui." The bigger your system grows and the more packages
you integrate into it, the more likely you are to find yourself running
`npm outdated` and seeing a wall of red that you simply close the terminal
and walk away from.

The fundamental problem is that version numbers are too small. A project on
version 3.2.1 does not *look* like it has momentum. A project on version
0.20.65536 clearly does.

As a solution to this problem, I propose a simple set of rules and requirements
that dictate how version numbers are assigned and incremented. These rules are
based on but not limited to practices already in widespread use in both
closed and open-source software.

I call this system "Exponential Versioning." Under this scheme, a version number
and the way it changes convey one thing: that the number is bigger than it was
before.

## Exponential Versioning Specification (ExpVer)

The key words "MUST", "MUST NOT", "REQUIRED", "SHALL", "SHALL NOT", "SHOULD",
"SHOULD NOT", "RECOMMENDED", "MAY", and "OPTIONAL" in this document are to
be interpreted as described in [RFC 2119](https://tools.ietf.org/html/rfc2119).

1. Software using Exponential Versioning MUST declare a public API. This API
could be declared in the code itself or exist strictly in documentation. However
it is done, it SHOULD be precise and comprehensive. It will not be referenced
again in this specification.

2. A normal version number MUST take the form ANCHOR.VIBES.EXPONENTIAL where
ANCHOR, VIBES, and EXPONENTIAL are non-negative integers, and MUST NOT contain
leading zeroes. ANCHOR is the anchor version, VIBES is the vibes version, and
EXPONENTIAL is the exponential version. Each element other than the ANCHOR MUST 
increase numerically. The EXPONENTIAL element MUST increase rapidly.

3. Once a versioned package has been released, the contents of that version
MUST NOT be modified. Any modification MUST be released as a new version.

4. ANCHOR version zero (0.y.z) is for initial development. The public API
SHOULD NOT be considered stable until it is shipped to production on at least
ten separate occasions.

5. The ANCHOR version SHOULD NOT be incremented after this point.

6. VIBES version z (x.z.E where x > 0) MUST be incremented if the maintainer
feels like it. It MAY be incremented for new functionality, bug fixes,
backwards-incompatible changes, documentation updates, whitespace changes,
or no changes at all. It MAY include EXPONENTIAL level changes. When the 
VIBES version is incremented, the EXPONENTIAL version MUST still be incremented 
per rule 7.

7. EXPONENTIAL version E MUST be incremented for every release, regardless
of what changed. The new EXPONENTIAL version MUST be computed as E raised
to the power of E (E^E). If the current EXPONENTIAL version is 0 or 1, it
MUST be set to 2. For example: 0 → 2 → 4 → 256 → 256^256 → …
The EXPONENTIAL version MUST NOT decrease. It MUST NOT remain unchanged
between releases.

8. A pre-release version MAY be denoted by appending a hyphen and a series of
dot-separated identifiers immediately following the EXPONENTIAL version.
Identifiers MUST comprise only ASCII alphanumerics and hyphens [0-9A-Za-z-].
Pre-release versions indicate that the version is unstable and might not satisfy
the intended compatibility requirements. In practice, the EXPONENTIAL version
already communicates this through sheer magnitude. Examples:
0.0.4-alpha, 0.3.256-rc.1.

9. Build metadata MAY be denoted by appending a plus sign and a series of
dot-separated identifiers immediately following the EXPONENTIAL or pre-release
version. Build metadata MUST be ignored when determining version precedence.
Examples: 0.0.4+build.42, 0.3.256+20260318.

10. Precedence refers to how versions are compared when ordered.

    1. Precedence MUST be calculated by comparing the ANCHOR, VIBES, and
    EXPONENTIAL versions, in that order. However, in practice, the EXPONENTIAL
    version alone is sufficient, as it grows monotonically and at a rate that
    makes collisions between independent releases a mathematical impossibility.

    2. When ANCHOR and VIBES are equal, the version with the larger EXPONENTIAL
    number always has higher precedence. Given the growth rate of the EXPONENTIAL
    version, this comparison is typically obvious to the naked eye.
    Example: 1.0.4 < 1.0.256 < 1.0.256^256.

    3. When two EXPONENTIAL versions have the same number of digits, something
    has gone wrong.

## Why Use Exponential Versioning?

This is not a new or revolutionary idea. In fact, you may already be doing
something close to this. Most software projects never make a breaking change
on purpose but will happily ship version 14.2.37891 anyway. The problem is not
that version numbers grow—it's that they grow without commitment. Exponential
Versioning provides a formal framework for the growth that is already happening,
and ensures that it happens with mathematical rigor.

Consider a set of packages called "Firetruck," "Ladder," and "Hose." Firetruck
depends on Ladder and Hose. Under Semantic Versioning, the maintainer of
Firetruck must understand the nature of every change in Ladder and Hose to
determine compatibility. Under Exponential Versioning, the maintainer simply
checks whether the EXPONENTIAL version is a larger number than before. It is.
It always will be.

Exponential Versioning frees the maintainer from the burden of communicating
the *nature* of a change through the version number, replacing it with a
single, unambiguous signal: the number went up. Dependencies are resolved
not through semantic reasoning but through arithmetic comparison—and thanks
to the growth rate, that comparison is rarely close.

## FAQ

**How should I deal with revisions in the 0.y.z initial development phase?**

Start your initial development release at 0.0.2 and exponentiate the
EXPONENTIAL version for each subsequent release. If you begin at 0.0.0, you
MUST immediately release 0.0.2, as 0^0 is either 1 or undefined depending
on your mathematical conventions, neither of which is useful here.

**How do I know when to release 1.0.0?**

Don't.

**Doesn't this discourage rapid development and fast iteration?**

No. Exponential Versioning encourages shipping as frequently as possible.
Each release produces a version number that is dramatically larger than the
last, providing clear and visible evidence of progress.

**If even the tiniest change requires an exponential bump, won't the version number become absurdly large very quickly?**

Yes.

**What do I do if I accidentally release a backwards-incompatible change?**

Release a new version with the incompatibility corrected. The EXPONENTIAL
version will increment as usual. Do not bump the ANCHOR version.

**What if I don't want my version numbers to be incomprehensibly large?**

Exponential Versioning may not be suitable for your project. Consider
[Semantic Versioning](https://semver.org/) instead.

**Does ExpVer have a size limit on the version string?**

No. The specification places no upper bound on the EXPONENTIAL version. Package
registries, file systems, and display devices may impose practical constraints.
These are implementation details and not the concern of the specification.

**What should I do if I update my own dependencies in a way that changes the public API?**

Increment the VIBES version if it feels right. The EXPONENTIAL version should be 
incremented regardless.

**How should I handle deprecating functionality?**

Mark the functionality as deprecated in your documentation. Release a new
version. The EXPONENTIAL version will communicate the magnitude of the change
more effectively than any deprecation notice.

**What if my entire team disagrees with this versioning scheme?**

Increment the VIBES version and revisit the conversation later.

## About

The Exponential Versioning specification was authored by
[Chris Morrell](https://cmorrell.com).

It is a loving parody of [Semantic Versioning](https://semver.org/), which is
a genuinely good specification that you should probably actually use.

## License

[Creative Commons ― CC BY 4.0](https://creativecommons.org/licenses/by/4.0/)
