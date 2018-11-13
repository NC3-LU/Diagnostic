# CHANGE LOG VERSION 1.2 NOVEMBER 2018

- Add language tab in the administration mode. It is now possible to add/modify/delete new languages and new translations.
- Add confirmation before deleting something(questions, categories, languages, translations).
- Add Reports tab in the administration mode in which we can download/upload report template modals.
- Add Settings tab in the administration mode in which we can change some global settings and add diagnostic statistics.
- Add importation/exportation for questions, categories and translations.
- Add an Uid for the Diagnostic, for the questions and for the categories.
- Add statistics importation for a diagnosis.
- Add several information in a diagnosis. We can now choose the activity of the company and its number of employees.
- Change the threshold calcul method for each question. It is now equal to threat*weight depends on the question.
- Add blocking question. Used if an essential domain for the entity is not managed.
- Update the evolution of maturity bar chart. We can now see the average of diagnoses of the current domain and overall diagnoses done for a year given.
- Add an help part for the organization and synthesis parts in a diagnosis.
- Aesthetics and ergonomics improved in the report. Better colors, N/A displayed instead of 0% out of 0%.
- Translation files now divided between questions, categories and translations for more visibility.
- Update documentation to match with new features.

<<<<<<< HEAD
=======

>>>>>>> test
# CHANGE LOG VERSION 1.1 JUNE 2018

- Update Ubuntu from 17.04 to 18.04.
- Update Php from 7.0 to 7.1.
- Add color to the category in the report (radar chart and tabs).
- Update the Diagnostic's logo from CASES to DIAGNOSTIC.
- Modify report to be more visual.
- Update calculation method (no more Planned Maturity, there is now a Non Applicable button).
- Display red points instead of triangles in the diagnostic to match with the MONARC convention.
- Use of OpenSSL to export and upload new diagnosis which wasn't working anymore since Php7.1.
- Add category tab in the adminitration mode. It is now possible to add/modify/delete new categories for the >Diagnostic.
- Update the administration mode. It is now possible to translate questions and categories without getting in >the .po files.
- Update documentation to match with new features.