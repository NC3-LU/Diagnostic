First connexion and Password change
===================================

![Connexion Screen](img/UG_Diagnostic_001.PNG)

The first step to do is to change the password. For that, just click on
the ‘Forgotten your Password?’ link, and put the mail which should be on
the database.

![Forgotten Your Password](img/UG_Diagnostic_002.PNG)

Then mail will be sent to you.

![Mail Send](img/UG_Diagnostic_003.PNG)

By clicking the link ‘NEW PASSWORD’ in the mail, you will get into a
page where you can change your password. You only need to put the new
one into the two text fields, and then just click on the ‘Change
Password’ button. If the mailing address is not found on the database,
the mail will not be sent.

![Change Password](img/UG_Diagnostic_004.PNG)

Then, you just need to create your first own diagnostic. For that, just
log in to the main connexion screen, by giving your mail and your new
password. Then, just click on the ‘Log in’ button.

> **Warning**
>
> If you are on the Virtual Machine, you will NOT have any mail server
> installed by default. So you wont receive any mail unless you install
> one. You can also use a script included in the virtual machine to
> change manually a password of any user (name\_of\_script.sh)

> **Tip**
>
> How to use the script, short description.

Begin a Diagnostic as a User
============================

![Main Page – Information about organisation](img/UG_Diagnostic_005.PNG)

On the header, at the right side (**1.** on the picture), you can set,
by clicking on the corresponding button, your language (**French** and
**English** are the two only choice right now). You can also disconnect
by clicking on the ‘Log out’ button. Also, if you are administrator of
the application, you should see the admin menu access button near the
red button use to disconnect.

On the top of the screen but below the header, at left, you could see a
button where you could just resume form an old diagnostic (**2.** on the
picture).

Just below, you have the navigation panel (**3.** on the picture), which
we will describe just a little later. Same for the two buttons used to
get the report or save the current Diagnostic (**4.** on the picture).

Finally, on the right of the screen, you have a free-text panel, where
you should put some text which will be in the report (**5.** on the
picture). Most of the time, the introduction is used to give the context
of this diagnostic, and some info that could be useful when you read the
report.

![Main Page – Information about organisation with
text](img/UG_Diagnostic_006.PNG)

Then, to save your work, you should just hit ‘Record and continue’. In
the navigation panel, the theme should be green to indicate that there
is already some text.

The summary, which is the last part of the Diagnostic, is a short
description of the most important points in it. The most important
recommendations, what should be done next…

Those are the only free text which is present.

After saving the first information, you will be redirected to the first
question.

As you can see in the navigation panel, there are eight big themes,
which can contain some questions. The red colour is mainly used on the
theme where you currently are. The question which is black and bold is
the current one. The green one is saved and contains text. By clicking
on the main theme, question about it will appear, and then you can go on
the questions by clicking on them.

The red cross near the question is for delete the question for this
diagnostic.

> **Warning**
>
> If you make a new one, the question will appeared again. If you want
> to make it disappear for all diagnostic that you make, you should just
> go in the administration panel to delete it.

![Main Page – Navigation Panel](img/UG_Diagnostic_007.PNG)

You can also add questions by clicking the red ![+ Add a
question](img/UG_Diagnostic_008.PNG) button.

> **Warning**
>
> Be careful, when you do add a question this way, it will be only last
> during this diagnostic. If you want to add a question for all yours
> diagnostic, you should add it in the administration part.

![Add Question](img/UG_Diagnostic_009.PNG)

You will have the question field to add the question as it will appear
into the report. The info field is for details which are displayed only
for the question creator. It’s useful to add details to the question, or
have a reminder of the main points to talk about.

The threshold is a little more difficult to use. To put it simply, this
is the weight in the category of the question.

Let’s take an example:

Imagine you need a specific question on the BYOD, and you think this
should be really important. You have, in the same category, a question
less important.

If the threshold are respectively 60 and 40, then the maturity on a
level is calculated this way: *((60/3 \* maturity) + (40/3 x maturity))*
where the maturity could be 0 if not managed, 1 if scheduled, 2 if more
or less managed and 3 if managed. So if a policy is scheduled about BYOD
and the other question is managed, the category will be *((60/3 x 1) +
(40/3 x 3)) = 20 + 40 = 60* of maturity.

Finally, just hit the green ‘Add’ button to add your new question and
get back on the main page.

![Main Page – First Question](img/UG_Diagnostic_010.PNG)

On the right side, you have a text field ‘Note’ where you can put what
you have seen, what has been said during the interview, precision about
what you want…

The maturity panel is where you can set the current maturity on a scale
of four levels (managed, more or less managed, scheduled, not managed).
You will also have bullet points to think when you ask the question,
what you should have in mind when you asking it, and what kind of answer
you should have.

The maturity target panel is the maturity level that the company should
have. It’s not necessarily managed, so the information security could be
adapted from a company to another.

The recommendation panel is the place where you could just put what the
company should do to have a better information security.

Finally, the gravity panel is to determine how much the recommendation
should be quickly implemented. For saving, as before, just hit ‘Record
and continue’.

Administration Panel
====================

User Tab
--------

You have two tabs (On the blue header), the first one is for Users, and
the second one is about the Questions.

![Administration Panel – User](img/UG_Diagnostic_011.PNG)

You can see all the mail addresses which are authorised to connect to
the diagnostic. You can click on the ![+ Add a
user](img/UG_Diagnostic_014.PNG) button, so you can add a user.

![Administration Panel – Add User](img/UG_Diagnostic_015.PNG)

You can put a mail address, choose if this account has access to this
interface, and just add it by clicking the blue button ‘Add’.

On the page where you can see all mail which is allowed to connect to
the Diagnostic, if you click on them, you should be able to modify the
address or choose whether it is admin or not.

![Administration Panel – Modify User](img/UG_Diagnostic_016.PNG)

> **Tip**
>
> The only way to modify a password is to get a password Forgotten link,
> or the script which is with the Virtual Machine.

Questions Tab
-------------

### Question Screen

The second tab list all the default questions that will appear when you
open a new Diagnostic.

![Administration Panel – Questions](img/UG_Diagnostic_017.PNG)

In the ‘Question’ column, you have all the questions that will appear.
The translation key is mainly used to link questions through all
languages. The category is, of course, the main theme linked, and the
threshold could be assimilated to the maturity that will bring a managed
control. To finish, the ‘action’ column represents the possibility to
edit the question (by clicking the pen
(![Pen](img/UG_Diagnostic_012.PNG))) or delete it (by clicking the cross
(![Cross](img/UG_Diagnostic_013.PNG))).

### Add a Question

You can also add questions by clicking the red ![+ Add a
question](img/UG_Diagnostic_008.PNG) button.

![Administration Panel – Add Questions](img/UG_Diagnostic_018.PNG)

The first field is for the translation key used by the PO file. The
built-in question is done by giving two underscores, the tag "question"
and the number of the questions (For example, "\_\_question33").

> **Tip**
>
> If the question is not found in the PO file, then the translation key
> will be displayed. So if you don’t intend to used these files, you can
> just put your question directly in the translation key.

There are no obligations about it.

You can also choose the category of the question, and its upper
threshold as if you were adding a question which is not definitive.

The diagnostic uses the PO files for translations. On the language
folder (\[*Path\_to\_Diagnostic*\]/languages), you will find the .po
files (uncompressed) and the .mo files (compiled).

You can modify manually .po files to add an entry with a text editor, or
a PO file editor (Like [POEdit](https://poedit.net/)).

![Edition of the PO File on Notepad++](img/UG_Diagnostic_019.PNG)

If you want to add the translation in another language, you need to
modify the other file (In this example, the language is fr\_FR).

![French translation of the question in
Notepad++](img/UG_Diagnostic_020.PNG)

Then, you will find the question in every diagnostic you will do.

![Question added](img/UG_Diagnostic_021.PNG)

### Change a Question

By editing, you will get on a similar interface as if you were adding a
question. You can change details on the same ways, as you will also need
to change .po and .mo files.

![Administration Panel – Change Questions](img/UG_Diagnostic_022.PNG)

### Delete a Question

Just click on the blue cross (![Cross](img/UG_Diagnostic_013.PNG)) to
definitly delete the question.

> **Warning**
>
> Be extremely careful, there is no confirmation message when you delete
> a question here.

Resume or finish a Diagnostic
=============================

Before your session ends for security reason, or if you want to resume
your diagnostic later, it is recommended to export often your work, by
hitting the yellow button below the navigation panel.

![Exported file](img/UG_Diagnostic_023.PNG)

Files are renamed by the following name: data\_yyyymmddhhnnss.cases
where

-   y = year

-   m = month

-   d = day

-   h = hour

-   n = minutes

-   s = second.

There are two ways to load this diagnostic. The first one, at the
connexion screen, doesn’t need to have an account to go on it.

![Connexion Screen](img/UG_Diagnostic_001.PNG)

By doing this, you will have only access to the report this way. It is
mostly used to have another quick way to show an overview of the report.
The other way is on the main page that you access just after getting
connected.

![Resume a Diagnostic](img/UG_Diagnostic_024.PNG)

Just on the top of the navigation panel, you can load the file that you
have downloaded, or that someone gives to you to resume or modify the
Diagnostic.

Report
======

Online Report
-------------

You can access to the screen report by just clicking on the yellow
button ![Report](img/UG_Diagnostic_025.PNG). You can also get this
screen without being connected, but you will not be able to download the
report as a ‘.docx’.

![Report Screen](img/UG_Diagnostic_026.PNG)

The first graph that you can see is the maturity by domains with the
risk cartography and more precisely with the tab on the right. You will
also find the recommendation tab which briefly summarises the
recommendations, their gravity and their current and target maturity.

![Recommendation Tab](img/UG_Diagnostic_027.PNG)

Just below the first tab, you will find the current maturity level and
the target level.

![Current and Target Maturity Level](img/UG_Diagnostic_028.PNG)

And you will also find the proportion of the category on the whole
Diagnostic.

![Proportion category](img/UG_Diagnostic_029.PNG)

Offline Report
--------------

If everything seems okay, you just need to get it on a .docx, and for
that, click on the yellow button ‘Download deliverable.’

![Download deliverable](img/UG_Diagnostic_030.PNG)

You will need to put a Document Name, the company which concerned by the
Diagnostic, the version of the document (If there are multiple
Diagnostics, or if you want to correct it…), a choice if it’s a draft or
a final version of the Diagnostic, the classification of the document
(who can read it or have it, it’s a free text, so it can be chosen with
TLP, or a classification on your own), and finally the name of the
consultant and the name of the client. Most of that data will be found
on the document. The document will be named \[*Document
Name*\]\_Date.docx.

![Report Downloaded](img/UG_Diagnostic_031.PNG)

In the document, you can find on the Part 1.1 the free text in
‘Information about organisation’ and on 2.1 the free text in ‘Summary of
evaluation’.

![Report Downloaded Part 2](img/UG_Diagnostic_032.PNG)

Graphics and tabs which were on the report screen could mostly be found
on in the document. a .do

![Report Downloaded Part 3](img/UG_Diagnostic_033.PNG)

There is also a tab which contains the questions, the note taken, the
recommendation and the current and target maturity.

Modify the template report
--------------------------

The template report is quite simple to understand. It can be found in :
\[*PATH\_TO\_DIAGNOSTIC*\]/data/resources. There is some tags which
corresponding to some fields in the diagnostic. You can find a complete
list just below. Concerning the charts, some dummy pictures are in the
document. Their name are "*image9.png*", "*image5.png*" and
"*image10.png*".

![Name of the dummy chart for the template](img/UG_Diagnostic_034.PNG)

And here is the dummy for the pie chart :

![Dummy in the report](img/UG_Diagnostic_035.PNG)

-   -   -   -   -   -   -   **$2017-09-19** : The date when is generated
    the report (Done automatically, depending of the server date)

-   -   -   -   **${PRISE\_NOTE**Y*X*}** *(Where X is a number under 1
    to 8 and Y is a number under 1 to 4)* : Contain a cross in the
    maturity Y for the current question in the category X (table
    of text)

-   **${PRISE\_NOTE**Y*X*}** *(Where X is a number under 1 to 8 and Y is
    a number under 1 to 2)* : Contain a cross in the aimed maturity Y
    for the current question in the category X (table of text)

-   **${PRISE\_NOTE\_CATEG*\_X*}** *(Where X is a number under 1 to 8)*
    : The name of the category/security domain field number X
    (Get automatically)

-   **${PRISE\_NOTE\_COLLECT*\_X*}** *(Where X is a number under 1
    to 8)* : The list of remarks corresponding to the question in the
    category number X (Recommandation field in a question, table
    of text)

-   **${PRISE\_NOTE\_RECOMM*\_X*}** *(Where X is a number under 1 to 8)*
    : The list of recommendation corresponding to the question in the
    category number X (Remarks field in a question, table of text)

-   **${PRISE\_NOTE\_TO\_COLLECT*\_X*}** *(Where X is a number under 1
    to 8)* : The list of question in the category number X (Get
    automatically, table of text)

-   -   -   -   -   -   -   -   
