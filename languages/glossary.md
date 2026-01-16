# Disciple.Tools Meetings - Glossary

This glossary defines key terms used in the Disciple.Tools Meetings plugin.

## Post Types

| Term | Definition |
|------|------------|
| **Meeting** | A record of an encounter between contacts and/or groups. The core post type of this plugin. |
| **Contact** | A person being tracked in Disciple.Tools (from the main DT theme). Meetings link to contacts as attendees or leaders. |
| **Group** | A community group, Bible study, or discipleship group (from the main DT theme). Meetings can be associated with a group. |

## Field Terms

| Term | Field Key | Definition |
|------|-----------|------------|
| **Date** | `date` | The date when the meeting occurred. Auto-populated with current date if not specified. |
| **Meeting Topic** | `meetings_topic` | The subject or agenda discussed during the meeting. |
| **Meeting Notes** | `meeting_notes` | Detailed notes recording what happened during the meeting. |
| **Meeting Type** | `type` | Classification of the meeting format (In Person or Online). |
| **Attendees** | `contacts` | The contacts/people who participated in the meeting. |
| **Leaders** | `leaders` | Contact(s) who led or facilitated the meeting. |
| **Group** | `groups` | The group associated with or for which the meeting was held. |
| **Assigned To** | `assigned_to` | The WordPress user responsible for managing the meeting record. |
| **Tags** | `tags` | Labels for categorizing and filtering meetings by characteristics. |

## Meeting Types

| Value | Definition |
|-------|------------|
| **In Person Meeting** | A face-to-face meeting where participants are physically present. |
| **Online Meeting** | A meeting conducted via audio or video call over the internet. |

## User Capabilities

| Capability | Definition |
|------------|------------|
| **access_meetings** | Permission to view meeting records. |
| **update_meetings** | Permission to edit existing meeting records. |
| **create_meetings** | Permission to create new meeting records. |

## UI Components

| Term | Definition |
|------|------------|
| **Tile** | A display section/grouping in the post detail view that organizes related fields together. |
| **List Filter** | A saved search configuration for filtering meetings in the list view. |

## Relationships (P2P)

| Relationship | Definition |
|--------------|------------|
| **meetings_to_contacts** | Links meetings to contacts as attendees. |
| **meetings_to_leaders** | Links meetings to contacts who led the meeting. |
| **meetings_to_groups** | Links meetings to the associated group. |

## Filter Hooks

| Hook | Definition |
|------|------------|
| **disciple_tools_meetings_types** | Filter to extend or customize available meeting type options. |

## Disciple.Tools Ecosystem Terms

| Term | Definition |
|------|------------|
| **Disciple.Tools (DT)** | An open-source discipleship and ministry management platform built on WordPress. |
| **DT Theme** | The WordPress theme that provides the core Disciple.Tools functionality. |
| **Module** | A component of Disciple.Tools that can be enabled or disabled. |
| **Post-to-Post (P2P)** | The relationship system used to connect different post types together. |
