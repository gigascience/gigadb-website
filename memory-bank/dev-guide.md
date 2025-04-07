This dev guide is meant to aid the developer to leverage this memory bank to optmially use AI in this codebase

The developer is encouraged to treat the AI as a smart junior developer that is good at small incremental changes to the code, provided clear directions and guidance (context)

Context is absolutely essential to work with AI as it directly determines the accuracy, usefuless and quality of the responses. How to reference a file as context depends on the tool being used, in Cursor, for example, you can use @ followed by a filename (or folder, or function name)

the memory-bank folder should contain all the files that provide context to the LLM, and used explicitly when needed

# Memory bank structure

the memory bank is composed of markdown files (could be other formats, but md is standard), whose purpose is to provide "memory" to the LLM. These files are provided as context to the LLM as required

- documentation.md: API endpoints, DB schemas, function specs, and architecture decisions
- project_milestones.md: project overview, goals, and phase breakdowns
- dev-guide.md: this file, its only purpose is to provide guidance to the developer, it is an instructions document for the human dev to use this memory bank
- prompts: AI needs to be told what to do. This folder contains useful prompts with common instructions for AI
- issues: files to keep track of the context for specific tickets to work on. I propose to create a folder for each ticket, named after the ticket number, and have any necessary context files within (e.g. outline.md, plan.md, etc)
- features: create one file for each feature within the codebase, and describe the feature. This can be used to easily provide context to the LLM as needed. Add feature files incrementally as per your needs

# Ticket clarification

It is critical to work on tasks where ambiguity is minimized

Facing a new ticket (new feature, bug, issue...), create a new file (e.g. `memory-bank/issues/1156/outline.md`) and write down what is known about the ticket

Ask the AI to read the `memory-bank/prompts/clarify.md` file and point it to the task to clarify

Iterate with the AI to clarify the task

Finally ask the AI to update the outline.md file with the clarifications, and review it manually for accuracy

# Codebase review

Ask the AI to review the codebase in relation to the task to identify what are the relevant files and functions

Ask it to follow the instructions in `memory-bank/prompts/review-codebase.md` and point it to the task outline file

The outline.md should be updated with the AI findings. Review it manually to make sure it makes sense, and update it as needed

# Planning

Use AI to write an implementation plan. Use a "thinking" AI model for that

As AI to read the `memory-bank/prompts/plan.md` instructions and apply them to the given task, referencing the task outline.md file

the I should write the implementation plan in a plan.md file sibling to the outline.md file

Read and review the output to crosscheck that it makes sense, and edit it as needed

# Implementation

I propose to directly copy paste each task from the implementation plan to the AI and work on it in. Make sure that the task itself is very small in scope, and always test it after it is done. Use a non-thinking model, e.g. Claude sonnet 3.5

After each task is implemented, consider adding tests if suitable

After all tasks have been implemented, you should add acceptance tests with the help of AI

# Acceptance tests

Write tests one at a time after plan has been implemented. Commit after each test passes

# Extra steps

- Add changelog after ticket is finished
- Write PR following a specific template
- Document any findings: architecture decisions, tech stack, feature description, ...

# Debugging

If debugging a challenging bug, it can be a good idea to follow a structured approach:

- Ask AI to inspect the code and pinpoint a handful of potential causes (e.g. 8)
- Ask AI to add logs to the code, such that the actual cause of the bug can be determined (ask it to mark the logs with something specific so they are easy to find and remove later)
- Manually inspect the logs, report to AI (paste the logs to the AI) and ask it to determine the cause of the bug from the logs
- Ask the AI to propose a fix
- Implement the fix

If you enter into a debugging loop with AI, where the bug does not seem to go away, stop, clear changes and return to the initial step, and start over, moving forward with smaller steps


# Refactor

To use AI to refactor a codebase, you could in principle follow an approach very similar to the one used for handling tickets

- Provide the refactoring criteria as context, and ask the AI to review the codebase in relation to the criteria, and generate a report or outline with the findings
- Use the generated report to write a refactoring plan that breaks the refactoring into small steps
- Apply the refactoring step by step
- Run tests after every step to make sure nothing breaks


# Yolo mode

If you want a more hands off approach during implementation of a plan, ask AI to add checkboxes to every task in the plan. Then simply keep on asking AI to find the first unchecked task in the plan, implement it and check it. Turn "yolo mode" on for your AI so that it does not ask you for permission to do anything. Use with caution. AI usually works best when supervised and aided by a human


# Feature analysis

Ask AI to analyze the codebase and explain a specific feature. Useful to quickly understand a part of the codebase we are not acquainted with. Provide it the instructions from `memory-bank/prompts/feature-analysis.md` and what feature you want described, e.g:

<instructions>
@feature-analysis.md
</instructions>

<feature>
server side pagination for samples table results in protected/views/dataset/view.php
</feature>