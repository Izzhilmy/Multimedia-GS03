# Project Overview — Gender Detection System (GS03)

## What This System Is

A Laravel web application that allows authenticated students to submit
their name, IC number, and a photo, then runs three gender retrieval
methods (ABR, TBR, CBR) and fuses them into a final gender prediction.
Results are stored per student and can be reviewed as history.

---

## The Problem Being Solved

Gender detection across multiple data modalities (metadata, text, image)
is non-trivial. This system demonstrates a multimedia database approach
by combining three retrieval strategies — attribute-based, text-based,
and content-based — into a single confident prediction.

---

## Who Uses This System

### Student (authenticated user)
A student who logs in using their matric number and password
from the shared `mmdb2026.stu` table. After login they can:
- Submit their name, IC number, and a photo
- Trigger all three retrieval analyses
- View the fused final gender result
- Browse their own past detection results (history)

There is no admin or lecturer role in this system.
There is no public access — login is required for everything.

---

## System Boundaries

This system does NOT:
- Allow public registration (credentials come from mmdb2026.stu)
- Write anything back to mmdb2026
- Perform real computer vision — CBR logic is rule-based on visual
  feature inputs provided by the student (hair length, hijab, facial hair)
- Support multiple students viewing each other's results
- Have an admin dashboard

---

## Key Constraints

| Constraint | Detail |
|---|---|
| Cross-DB login | Auth reads mmdb2026.stu; never writes to it |
| Project DB | gs03, password 1234 |
| Storage | Uploaded images in storage/app/public/uploads/ |
| Detection | ABR + TBR + CBR all three, fused by majority vote |
| History | All results saved, student can view their own only |
| Stack | Laravel + Blade, plain PHP views, MySQL |

---

## Success Definition

The system is successful when:
- A student can log in with their matric_no and password
- They can upload a photo, enter name and IC, and get a gender result
- All three retrieval methods run and produce a fused final result
- The result is saved and visible in the student's history
- No student can see another student's data
