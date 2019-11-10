# Test Driven
In order to work more efficiently in the long run, to be able to refactor with 
confidence, to build with deliberation and to not break things things that were 
working just fine the other day, a test-driven approach is maintained.

## The value of tests
Many authors have written and spoken about the negative value of bad tests. 
Bad tests are so common that many confuse bad tests with unit tests in general, 
and mistakenly criticise the entire concept of unit testing or TDD.

While the criticism of unit testing are often valid when applied to bad tests, 
they often miss the point completely (just like those bad tests) when it comes 
to (well-applied) TDD.

Bad tests cost more than their value. Any cost will be more than their value - 
the value of truly bad tests is actually negative.

What makes a good test? If there's cost and potential negative value, why even 
"risk" writing tests?

The value of a good test comes from its ability to determine whether the 
behaviour of a component works as intended. If the test suite is able to 
determine if the end result meets the expectations without being coupled to the 
implementation details, the production code can be refactored with confidence: 
if the refactoring broke any previously built behaviour, the tests will quickly 
indicate which expectation isn't met anymore.

### Bad tests
A bad test will lock the implementation in place, rather than test the behaviour 
of a component. It breaks when the code is refactored, even if the application 
still works.

Tests like this will not help us in determining whether the application still 
works - on the contrary! If we'd have bad tests like that, a failing test means 
extra work: determining whether it was indeed a bad test, figuring out what the 
test was testing, whether a code fix or a test rewrite is needed...

In test environments that heavily rely on mocking, it is often customary to fix 
the tests after a refactor, for instance by reconfiguring the mocks.

The kind of tests that need to be "maintained" when you change the code but not 
the behaviour, those are the tests we do not want to see.

### Test first
Bad tests are often written after the code was produced. Writers of bad tests 
probably find TDD rather silly: you can't lock the implementation in a test if 
you haven't yet implemented anything.

To a TDD practitioner, such rhetoric sounds silly indeed; like a carpenter who 
claims not to be able to measure anything until *after* they sawed the plank.

The carpenter understands it's not the sawed plank itself, per se, that is most 
important to measure (only to assert that the length is indeed correct) 
The most important measurement is made up front: the size of the space where 
the plank is to be used.

Measuring the length for a plank comes down to measuring the intended goal, 
rather than measuring the resulting plank.
In the same spirit, writing a test for the code ought to come down to testing 
the intended goal, rather than testing the resulting implementation.

Since requirements tend to change during the process of building whatever was 
specified, it makes little sense to measure (write tests for) *all* the 
envisioned functionality up front.
When all the tests are made up front, agility goes down, because the tests 
continuously need updates. As does motivation, because tests keep failing until
the very end of the project.

Instead, tests for this project are mainly written right before implementing the 
functionality required to make the test pass.

### Good tests
A good suite of tests, is a test suite that tells us whether the application 
will work if we deploy it.
By extension, a good test is a test that helps reaching that goal.

The real value of a test comes from its ability to check if the application 
behaves as expected from it.
To achieve this value, it is important that each *unit* test tests a *unit of 
behaviour* rather than a *unit of code*.

## Test scope
The unit tests in this project interact with the application by sending 
commands and making assertions on the read models.

An example could be:
```php
$this->handle(StartTheMatch::forProposal($this->proposal, $correlationId));

$match = $this->ongoingMatches->forProposal($this->proposal);

$this->assertCount(7, $this->cardsInTheHand->ofPlayer(1, $match->id()));
```
Or
```php
$this->handle(ProposeMatch::between(
    $this->accountOne,
    $this->accountTwo,
    $this->clock->now(),
    $this->id
));
$proposalId = $this->matchProposals->for($this->accountTwo)[0]->id();

$this->handle(AcceptTheProposal::withId(
    $proposalId,
    $this->accountTwo,
    $this->clock->fastForward($this->aLittleTooLong)->now(),
    $this->id
));

$this->assertEmpty(
    $this->acceptedProposals->since($this->allBegan)
);
$this->assertEquals(
    ['The proposal has already expired!'],
    $this->refusals->for($this->id)
);
```

The unit test suite can be configured with several test configurations. This 
way, multiple infrastructure implementations can be tested using the same 
behaviour checks.

[Command handlers](CQRS.md#command-handlers) can either be supplied with 
in-memory implementations of the repositories, for extreme speed, or with 
repositories that touch the database, to test the persistence layer.
Configurations that postpone all commands by certain amount of time, either by 
waiting for real or by fast-forwarding the clock, are also planned.

## Coverage
Obviously, the aim is to achieve 100% coverage. But coverage of what?

There are many ways to measure code coverage, but most are limited to measuring 
which lines are touched while running the tests. Since it [can be very easy](https://github.com/Stratadox/PullRequestHelper) 
to achieve high code coverage [without testing anything](https://martinfowler.com/bliki/AssertionFreeTesting.html), 
code coverage should be interpreted with care and only used as a [metric, not 
as goal](https://en.wikipedia.org/wiki/Goodhart%27s_law).

The true 100% coverage mark that is aimed for, is not code coverage - be it line, 
branch or mutation coverage - but *intention coverage*.

### Intention coverage
Aiming for intention coverage is a delicate task. No metrics exist for measuring 
how well your intentions are covered by your tests. Only the amount of times QA 
hands back the project and the amount of bugs raised by consumers gives a clue.
Both of those feedback loops are slow - and preferably avoided - leaving only 
human scrutiny and the test-driven process as guiding principles.

To get as close to 100% intention coverage as possible, the "lesser" coverage 
metrics are analysed as well, although used as feedback rather than as goal to 
strive towards.

Even though these "lesser" coverage metrics cannot tell with certainty whether 
the intention coverage goal is reached, they can help in pointing out areas of 
the code - or the tests - where that intention coverage is lacking.

### Line coverage
Line coverage is an unreliable metric in determining how much of our intentions 
are covered by tests. There's an extreme likelihood of false positives: any line 
of code that is *executed* in any way is seen as covered, even though it may 
have had nothing to do with achieving the goal of the test that covered it.

False negatives, however, are much rarer. When a line of code is never executed 
during the testing process, it cannot be a line that is helping to achieve the 
goals that are set forth by the tests.

In this project, line coverage is mainly used to determine whether a line can be 
purged from the codebase. Lines of code that might have been useful in the past, 
that were written in overzealous efforts or unused for other reasons, are 
ruthlessly removed.

### Mutation Score Indicator
Mutation testing is a process whereby a copy of the production code repeatedly 
gets a tiny random alteration by a mutator. A mutator might change `true` into 
`false`, `<` into `>`, `===` into `!==`, remove entire lines, or any other 
"silly" change that will probably break the code.
In this project, the [infection](https://infection.github.io/guide/) framework 
is used.

The tests are ran on each of these mutations. Since the code was changed in a 
weird way, the tests are expected to somehow indicate the problem. If the tests 
still pass, even though the code has been mutated, that mutation is said to have 
"escaped".

Escaped mutants are not always problematic. While some are harmless and 
inconsequential mutations that do not need immediate attention, oftentimes the 
fact that the mutation escaped gives an insight about which parts of the code 
could use better testing.

Mutation testing results might lead to public methods made private, unneeded 
properties and methods being removed or additional tests being written.

### Metrics, not goals
It can be tempting to see 100% line coverage or 100% MSI as goals. They should 
not, however, be seen as such.

#### Line coverage is not a goal
This project has, at the time of writing, a line coverage of 98.8% and mutation
score indicator of 88%. Line coverage is high because code is generally written 
in order to make a test pass, and lines that are not touched by tests are often 
purged.
Not all untouched lines of code can be removed with impunity. Sometimes, methods 
are required by an interface, even if they're never called in reality.

It would be possible to write tests that cover those methods. 
Doing so would increase the test coverage, potentially to 100% with relatively 
little effort.

Since those tests would not be checking whether the application works as 
expected, but instead only exist to increase the coverage, their actual value is 
extremely low. 
On the one hand, they don't test business value. 
On the other, they lock the implementation in place. 
As bonus, they would reduce the indicative value of the line coverage: those 
lines were not required in order to fulfill the actual expectations; having the 
lines marked as though they were does not truly help anyone.

#### MSI is not a goal
Although the mutation score indicator score is presumably closer to the sought 
after intention coverage than line coverage could ever hope to be, having 100% 
MSI as a goal might be an even sillier proposition.
The mutation score depends on the available mutators as much as on the system 
under test.
Many mutators will produce mutations that are not problematic in the slightest.

The goal of mutation testing is to randomly generate changes to the code, in the 
assumption that a change to the implementation will trigger a change in the 
behaviour as well. In cases where the random change alters the behaviour, and 
the change in behaviour is not picked up by the tests, action is probably 
needed. 

A good test, however, passes when the behaviour of the system under test works 
as expected, without being coupled to the implementation. While most mutations 
might indeed trigger a change in behaviour, those that do not ought not lead to 
any change to the code nor to the tests.

When the number of mutators increases, the MSI could either in- or decrease, 
depending on how many are caught by the existing tests. Whichever direction the 
change in MSI goes, the chance of generating a mutation that does not alter the 
behaviour increases either way.

Given the above, the only way of reaching and keeping a 100% mutation score is 
by artificially limiting the mutators.
Although mutators that will only produce false positives can safely be disabled 
(currently the mutators that change `===` into `==` and `!==` into `!=` are 
turned off) it damages the value of the metric by removing valuable mutators for 
the sake of increasing the MSI.

#### PHPStan is a goal
[PHPStan](https://github.com/phpstan/phpstan), being a static code analyser with 
a pass-or-fail check on multiple levels of strictness, should pass with at least 
the current highest level.

As a tool, PHPStan simply passes a bunch of checks on whether the internal type 
system is consistent. There is little reason not to demand that all the code is 
internally consistent with each other.
