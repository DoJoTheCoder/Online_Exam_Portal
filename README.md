# Online_Exam_Portal
An online examination website containing admin features for exam modification and viewing proctoring information and student features to take MCQ exam in the given timespan.

## Home page:
- Contains a page where the user can select the admin page or student page.

## Admin page featues:
- Admin log-in
- Uploading excel files containing questions, options and answers in MCQ format
- CRUD table for easy editing of the Exam questions
- Organise Exams into Subjects, which can be added by admin.
- Invigilator ID and password can be added by admin, which is required by students to take the exam.
- Proctoring information such as Geolocation and webcam pictures taken during exam can be viewed for individual students who took an exam.

## Student page features:
- Student log-in and registration
- MCQ exams can be taken from the list of available exams added by the admin. 
- Before starting the exam, webcam and geolocation permissions are asked, along with Invigilator ID and password to access the exam.
- The exam page has a 1 hour timer, a single page with all the questions and options, and a submit button. The exam can only be taken in full screen.
- During the exam, the geolocation coordinates and a webcam picture is taken once in every 1 minute interval. These proctoring details are randomly taken anytime within the 1 minute interval.
- The options chosen during the exam and time left for exam are stored in cookies, so if the exam gets interupted due to any technical issue, the user can join the exam later without losing progress.
- After taking the test, the user can see his score, the questions where the user made mistakes and the correct answers in the results page.

## Additional Information:
- Database used: MySQL
- Webcam images taken during an exam are stored in the database in dataURL form.
- Uses Bootstrap CSS
