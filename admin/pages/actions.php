<?php

// Get count of draft courses
$draftCoursesQuery = "SELECT COUNT(*) as draft_count FROM " . $siteprefix . "courses WHERE status = 'draft'";
$draftResult = mysqli_query($con, $draftCoursesQuery);
$draftCourses = mysqli_fetch_assoc($draftResult)['draft_count'];

// Get total users count
$usersQuery = "SELECT COUNT(*) as user_count FROM " . $siteprefix . "users";
$usersResult = mysqli_query($con, $usersQuery);
$totalUsers = mysqli_fetch_assoc($usersResult)['user_count'];

// Get total enrolled courses count
$enrolledQuery = "SELECT COUNT(*) as enrolled_count FROM " . $siteprefix . "enrolled_courses";
$enrolledResult = mysqli_query($con, $enrolledQuery);
$totalEnrolled = mysqli_fetch_assoc($enrolledResult)['enrolled_count'];

// Get today's registrations
$today = date('Y-m-d');
$todayUsersQuery = "SELECT COUNT(*) as today_users FROM " . $siteprefix . "users WHERE DATE(created_date) = '$today'";
$todayUsersResult = mysqli_query($con, $todayUsersQuery);
$todayUsers = mysqli_fetch_assoc($todayUsersResult)['today_users'];

// Get today's enrollments
$todayEnrollmentsQuery = "SELECT COUNT(*) as today_enrolled FROM " . $siteprefix . "enrolled_courses WHERE DATE(start_date) = '$today'";
$todayEnrollmentsResult = mysqli_query($con, $todayEnrollmentsQuery);
$todayEnrollments = mysqli_fetch_assoc($todayEnrollmentsResult)['today_enrolled'];


$sql = "SELECT * FROM dv_alerts WHERE status='0' ORDER BY s DESC LIMIT 5";
$sql2 = mysqli_query($con,$sql);
$notification_count = mysqli_num_rows($sql2);
 
if (isset($_GET['action']) && $_GET['action'] == 'read-message') {
    $sql = "UPDATE dv_alerts SET status='1' WHERE status='0'";
    $sql2 = mysqli_query($con,$sql);
    $message="All notifications marked as read.";
    showToast($message);
    header("refresh:2; url=notifications.php");
}

if(isset($_POST['update-profile'])){
    $fullName = htmlspecialchars($_POST['fullName']);
    $email = htmlspecialchars($_POST['email']);
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
    $retypePassword = !empty($_POST['retypePassword']) ? $_POST['retypePassword'] : null;
    $oldPassword = htmlspecialchars($_POST['oldpassword']);
    $profilePicture = $_FILES['profilePicture']['name'];

    // Validate passwords match
    if ($password && $password !== $retypePassword) {
        $message= "Passwords do not match.";
    }

    // Validate old password
    $stmt = $con->prepare("SELECT password FROM ".$siteprefix."users WHERE s = ?");
    if ($stmt === false) {
        $message = "Error preparing statement: " . $con->error;
    } else {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        if ($user === null || !checkPassword($oldPassword, $user['password'])) {
            $message = "Old password is incorrect.";
        }
    }

    $uploadDir = '../../uploads/';
    $fileKey='profilePicture';
    global $fileName;

    // Update profile picture if a new one is uploaded
    if (!empty($profilePicture)) {
        $profilePicture = handleFileUpload($fileKey, $uploadDir, $fileName);
    } else {
        $profilePicture = $profile_picture; // Use the current profile picture if no new one is uploaded
    }

    // Update user information in the database
    $query = "UPDATE ".$siteprefix."users SET name = ?, email = ?, profile_picture = ?";
    $params = [$fullName, $email, $profilePicture];

    if ($password) {
        $query .= ", password = ?";
        $params[] = $password;
    }

    $query .= " WHERE s = ?";
    $params[] = $user_id;

    $stmt = $con->prepare($query);
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    if ($stmt->execute()) {
        $message= "Profile updated successfully.";
    } else {
        $message= "Error updating profile.";
    }
    showToast($message); 
    echo "<meta http-equiv='refresh' content='2'>";
}

if (isset($_POST['update-user'])) {
    // Sanitize inputs
    $userid = intval($_POST['userid']);
    $fullName = mysqli_real_escape_string($con, trim($_POST['fullName']));
    $email = mysqli_real_escape_string($con, trim($_POST['email']));
    $type = mysqli_real_escape_string($con, trim($_POST['type']));
    $status = mysqli_real_escape_string($con, trim($_POST['status']));
    $password = trim($_POST['password']); // Optional

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message="Invalid email format";
        exit;
    }

    // Build base query
    $updateQuery = "UPDATE " . $siteprefix . "users SET name = '$fullName', email = '$email', type = '$type', status = '$status'";

    // Append password update if provided
    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hash the password
        $updateQuery .= ", password = '" . mysqli_real_escape_string($con, $hashedPassword) . "'";
    }

    $updateQuery .= " WHERE s = $userid";

    // Execute query
    if (mysqli_query($con, $updateQuery)) {
        $message="User record updated successfully!";
    } else {
        $message="Failed to update user: " . mysqli_error($con) . "";
    }

    showToast($message);
}



//send message
if (isset($_POST['sendmessage'])) {
    $subject = htmlspecialchars(trim($_POST['title']), ENT_QUOTES, 'UTF-8');
    $content = htmlspecialchars(trim($_POST['content']), ENT_QUOTES, 'UTF-8');
    $recipientSelection = $_POST['user']; // For arrays, sanitize later

    // Initialize recipient list and names
    $recipients = [];
    $recipientNames = [];

    // Handle recipient selection
    if (in_array('all', $recipientSelection)) {
        // Query all users excluding admins
        $query = "SELECT email, name FROM " . $siteprefix . "users WHERE type != 'admin'";
    } elseif (in_array('instructor', $recipientSelection)) {
        // Query instructors only
        $query = "SELECT email, name FROM " . $siteprefix . "users WHERE type = 'instructor'";
    } elseif (in_array('user', $recipientSelection)) {
        // Query regular users only
        $query = "SELECT email, name FROM " . $siteprefix . "users WHERE type = 'user'";
    } else {
        // Add specific user emails
        foreach ($recipientSelection as $email) {
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                // Fetch name for individual users
                $individualQuery = "SELECT name FROM " . $siteprefix . "users WHERE email = '$email'";
                $result = mysqli_query($con, $individualQuery);
                if ($result && $row = mysqli_fetch_assoc($result)) {
                    $recipients[] = $email;
                    $recipientNames[$email] = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
                } else {
                    $recipients[] = $email;
                    $recipientNames[$email] = 'Valued User'; // Default name
                }
            }
        }
    }

    // If a query is needed for group selections, execute and fetch emails and names
    if (!empty($query)) {
        $result = mysqli_query($con, $query);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $email = $row['email'];
                $name = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $recipients[] = $email;
                    $recipientNames[$email] = $name;
                }
            }
        }
    }

    // Remove duplicates
    $recipients = array_unique($recipients);

    // Send emails
    foreach ($recipients as $email) {
        $name = $recipientNames[$email] ?? 'Valued User'; // Default to "Valued User" if no name
        $personalizedContent = str_replace('{{name}}', $name, $content); // Replace {{name}} in content

        if (sendEmail($email, $name, $siteName, $siteMail, $personalizedContent, $subject)) {
            $message = "Message sent to $name ($email)";
            showToast($message);
        } else {
            $statusAction="Failed";
            $message = "Failed to send message to $name ($email)";
            showErrorModal($statusAction, $message);
        }
    }
}





//register user
if(isset($_POST['register'])){
    $fullName = $_POST['fullName'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $retypePassword = $_POST['retypePassword'];
    $options = $_POST['options'];
    $type = $_POST['type'];
    
    
     //status
     $status='active';
     $date=date('Y-m-d H:i:s');

//error for same email,password errors
$checkEmail = mysqli_query($con, "SELECT * FROM ".$siteprefix."users WHERE email='$email'");
if(mysqli_num_rows($checkEmail) >= 1 ) {
$statusAction="Ooops!";
$statusMessage="This email has already been registered. Please try registering another email.";
showErrorModal($statusAction, $statusMessage); } 					
else if (strlen($password) < 6){
    $statusAction="Try Again";
    $statusMessage="Password must have 8 or more characters";
    showErrorModal($statusAction, $statusMessage);
}										
else if ($password !== $retypePassword ){
    $statusAction="Ooops!";
    $statusMessage="Password do not match!";
    showErrorModal($statusAction, $statusMessage);
}
else {
    $password=hashPassword($password);
    
    $submit = mysqli_query($con, " INSERT INTO `".$siteprefix."users` (`google_id`,`name`, `email`, `password`, `type`, `reward_points`, `created_date`, `last_login`, `email_verify`, `status`,`profile_picture`,`preference`)
     VALUES ('','$fullName', '$email', '$password', '$type', 0, '$date', '$date', 1, '$status','','$options')")
    or die('Could not connect: ' . mysqli_error($con));
    $user_id = mysqli_insert_id($con);
    

    $emailSubject="Registration Successful";
    $emailMessage="<p>Thank you for registering on our website. Your registration is now complete. 
    You can now log in using your email and password<br> Email: $email <br> Password: $password</p>";
    $adminmessage = "A new user has been registered($name - $account)";
    $link="users.php";
    $msgtype='New User';
    $message_status=0;
    $emailMessage_admin="<p>Hello Dear Admin,a new user has been successfully registered!</p>";
    $emailSubject_admin="New User Registeration";
    insertadminAlert($con, $adminmessage, $link, $date, $msgtype, $message_status); 
    //sendEmail($email, $name, $siteName, $siteMail, $emailMessage, $emailSubject);
    //sendEmail($siteMail, $adminName, $siteName, $siteMail, $emailMessage_admin, $emailSubject_admin);
    echo header("location:users.php");	
    }
}


  //Edit course
  if (isset($_POST['updatecourse'])) {
    $course_id = $_POST['course_id'];
    $sql = "SELECT * FROM " . $siteprefix . "courses WHERE s = '$course_id'";
    $result = mysqli_query($con, $sql);
    $course = mysqli_fetch_assoc($result);


        $title = mysqli_real_escape_string($con, $_POST['title']);
        $description = mysqli_real_escape_string($con, $_POST['description']);
        $category = $_POST['category'];
        $type = $_POST['type'];
        $level = $_POST['level'];
        $status = $_POST['status'];
        $error = "";

        if ($_FILES['featured']['name']) {
            $uploadDir = '../../uploads/';
            $fileKey = 'featured';
            global $fileName;
            $picture = handleFileUpload($fileKey, $uploadDir, $fileName);
        } else {
            $picture = $course['featured_image'];
        }

        $query = "UPDATE " . $siteprefix . "courses SET 
            title = '$title', 
            description = '$description', 
            language = '$category', 
            type = '$type',
            level = '$level',
            featured_image = '$picture', 
            updated_date = NOW(), 
            updated_by = '$name', 
            status = '$status' 
            WHERE s = '$course_id'";

        $submit = mysqli_query($con, $query);
        if ($submit) {
            $statusAction = "Successful";
            $statusMessage = "Course updated successfully! $error";
            showSuccessModal($statusAction, $statusMessage);
            header("refresh:1; url=courses.php");
        } else {
            $statusAction = "Oops!";
            $statusMessage = "An error has occurred!";
            showErrorModal($statusAction, $statusMessage);
        }
    }



    

    if (isset($_POST['updatesection'])) {
        $title = mysqli_real_escape_string($con, $_POST['title']);
        $subtitle = mysqli_real_escape_string($con, $_POST['subtitle']);
        $content = mysqli_real_escape_string($con, $_POST['content']);
        $type = $_POST['type'];
        $section = $_POST['section'];
        $duration = $_POST['duration'];
        $course = $_POST['course'];
        $chapter = $_POST['chapter'];
        $previouslink = $_POST['previous'];
        $error = "";
    
        // Handle file upload only if new file is selected
        if (!empty($_FILES['media']['name'])) {
            $uploadDir = '../../uploads/';
            $fileKey = 'media';
            global $fileName;
            $media = handleFileUpload($fileKey, $uploadDir, $fileName);
            
            // Update query with new media
            $query = "UPDATE `".$siteprefix."theory` SET 
                title = '$title',
                subtitle = '$subtitle',
                duration = '$duration',
                content_type = '$type',
                content = '$content',
                chapter = '$chapter',
                media_content = '$media',
                updated_on = NOW()
                WHERE s = '$section'";
        } else {
            // Update query without changing media
            $query = "UPDATE `".$siteprefix."theory` SET 
                title = '$title',
                subtitle = '$subtitle',
                duration = '$duration',
                content_type = '$type',
                content = '$content',
                chapter = '$chapter',
                updated_on = NOW()
                WHERE s = '$section'";
        }
    
        $submit = mysqli_query($con, $query);
        
        if ($submit) {
            $statusAction = "Successful";
            $statusMessage = "Section updated successfully! <br>
            <a href='sections.php?course=$course'>View More Sections</a>";
            showSuccessModal($statusAction, $statusMessage);
        } else {
            $statusAction = "Oops!";
            $statusMessage = "An error has occurred!";
            showErrorModal($statusAction, $statusMessage);
        }
    }

    

//create course
if (isset($_POST['addcourse'])){
    $title = mysqli_real_escape_string($con, $_POST['title']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $category = $_POST['category'];
    $type = $_POST['type'];
    $level = $_POST['level'];
    $status = $_POST['status'];
    $error="";


    $uploadDir = '../../uploads/';
    $fileKey='featured';
    global $fileName;
    $picture = handleFileUpload($fileKey, $uploadDir, $fileName);

    $query = "INSERT INTO `".$siteprefix."courses` (`s`, `title`, `description`, `language`, `type`,`level`,`featured_image`, `created_date`, `updated_date`, `updated_by`, `status`) 
    VALUES (NULL, '$title', '$description', '$category', '$type','$level','$picture', NOW(), NOW(), '$user_id', '$status')";
    $submit = mysqli_query($con, $query);
    $s_value = mysqli_insert_id($con);
    if ($submit) { 
        $statusAction="Successful";
        $statusMessage="Course created successfully! $error";
        showSuccessModal($statusAction,$statusMessage);
        header("refresh:1; url=addsection.php?course=$s_value");
    } 
    else { die('Could not connect: ' . mysqli_error($con));
        $statusAction="Oops!";
        $statusMessage="An error has occured!";
        showErrorModal($statusAction,$statusMessage); }
   
}


        // Handle delete question
        if (isset($_POST['delete_question'])) {
            $question_id = mysqli_real_escape_string($con, $_POST['question_id']);
            $quiz_id = mysqli_real_escape_string($con, $_POST['quiz_id']);
            
            // First delete associated options
            mysqli_query($con, "DELETE FROM {$siteprefix}quiz_options WHERE question_id = '$question_id'");
            
            // Then delete the question
            mysqli_query($con, "DELETE FROM {$siteprefix}quiz_questions WHERE s = '$question_id'");
            
            header("Location: edit-quiz.php?id=" . $quiz_id);
            exit();
        }

        // Handle update question
        if (isset($_POST['update_question'])) {
            $question_id = mysqli_real_escape_string($con, $_POST['question_id']);
            $quiz = mysqli_real_escape_string($con, $_POST['quiz_id']);
            $question_text = mysqli_real_escape_string($con, $_POST['question_text']);
            
            // Update question
            $updateQuestion = "UPDATE {$siteprefix}quiz_questions 
                      SET question = '$question_text' 
                      WHERE s = '$question_id'";
            mysqli_query($con, $updateQuestion);

            // Update options
            if (isset($_POST['options'])) {
            foreach ($_POST['options'] as $option_id => $option_text) {
                $option_id = mysqli_real_escape_string($con, $option_id);
                $option_text = mysqli_real_escape_string($con, $option_text);
                $is_correct = 0;
                
                if (isset($_POST['correct_option'][$question_id]) && 
                $_POST['correct_option'][$question_id] == $option_id) {
                $is_correct = 1;
                }

                $updateOption = "UPDATE {$siteprefix}quiz_options 
                       SET option_text = '$option_text', 
                           is_correct = $is_correct 
                       WHERE s = '$option_id'";
                mysqli_query($con, $updateOption);
            }
            }

            header("Location: edit-quiz.php?id=" . $quiz);
            exit();
        }



//addsection
if (isset($_POST['addsection'])){
    $title = mysqli_real_escape_string($con, $_POST['title']);
    $subtitle = mysqli_real_escape_string($con, $_POST['subtitle']);
    $content = mysqli_real_escape_string($con, $_POST['content']);
    $type = $_POST['type'];
    //$section = $_POST['section'];
    $duration = $_POST['duration'];
    $course = $_POST['course'];
    $error="";

    $uploadDir = '../../uploads/';
    $fileKey='media';
    global $fileName;
    $media = handleFileUpload($fileKey, $uploadDir, $fileName);

    if($type=="text"){$media="";}

// Query to get the last chapter value
$query_last_chapter = "SELECT chapter FROM `".$siteprefix."theory` WHERE course_id = '$course' AND chapter IS NOT NULL ORDER BY chapter DESC LIMIT 1";
$result_last_chapter = $con->query($query_last_chapter);
if ($result_last_chapter && $result_last_chapter->num_rows > 0) {
    $last_chapter = $result_last_chapter->fetch_assoc()['chapter'];
    $new_chapter = $last_chapter + 1;
} else {
    $new_chapter = 1; // No existing entries, set chapter to 1
}
$section = $new_chapter;


    $query = "INSERT INTO `".$siteprefix."theory`(`s`, `course_id`, `chapter`, `title`, `subtitle`, `duration`, `content_type`, `content`, `media_content`, `updated_on`)
     VALUES (NULL, '$course', '$section', '$title', '$subtitle', '$duration', '$type', '$content', '$media', NOW())";
    $submit = mysqli_query($con, $query);
    $s_value = mysqli_insert_id($con);
    if ($submit) { 
        $statusAction="Successful";
        $statusMessage="Section added successfully! Add more or proceed to manage courses <a href='courses.php'>View courses</a>";
        showSuccessModal($statusAction,$statusMessage);
    } 
    else { die('Could not connect: ' . mysqli_error($con));
        $statusAction="Oops!";
        $statusMessage="An error has occured!";
        showErrorModal($statusAction,$statusMessage); }
   
}


//create quiz
if (isset($_POST['addquiz'])){
    $title = mysqli_real_escape_string($con, $_POST['title']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $course = $_POST['course'];
    $timer = $_POST['duration'];
    $points = $_POST['points'];
    $language = getLanguagebyCourse($course);
   
    $query = "INSERT INTO `dv_quiz` (`s`, `language_id`, `course_id`, `title`, `description`, `timer`, `updated_at`, `points`)
     VALUES (NULL, '$language', '$course', '$title', '$description', '$timer', NOW(), '$points')";
    $submit = mysqli_query($con, $query);
    $s_value = mysqli_insert_id($con);
    if ($submit) { 
        $statusAction="Successful";
        $statusMessage="Quiz created successfully!";
        showSuccessModal($statusAction,$statusMessage);
        header("refresh:1; url=addquestions.php?quiz=$s_value");
    } 
    else { die('Could not connect: ' . mysqli_error($con));
        $statusAction="Oops!";
        $statusMessage="An error has occured!";
        showErrorModal($statusAction,$statusMessage); }
   
}

if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $table = $_GET['table'];
    $item = $_GET['item'];
    $page = $_GET['page'];
    
    if (deleteRecord($table, $item)) {
        $message="Record deleted successfully.";
    } else {
         $message="Failed to delete the record.";
    }

    showToast($message);
    header("refresh:1; url=$page");
}


if (isset($_POST['addquestions'])){
    // Get form data
    $questions = $_POST['questions']; // Array of questions
    $options = $_POST['options'];     // Flat array of all options
    $correctAnswers = $_POST['correct']; // Array of correct option indexes for each question

    // Quiz ID (if required)
    $quiz_id = $_POST['quiz']; // Replace with actual quiz ID if necessary

    // Option counter to track options across multiple questions
    $optionIndex = 0;

    foreach ($questions as $qIndex => $question) {
        // Insert question into dv_quiz_questions
        $stmt = $con->prepare("INSERT INTO dv_quiz_questions (`quiz_id`, `question`, `updated_at`) VALUES (?, ?, NOW())");
        $stmt->bind_param('is', $quiz_id, $question);
        $stmt->execute();

        // Get the inserted question ID
        $question_id = $stmt->insert_id;
        $stmt->close();

        // Insert options for the current question
        for ($i = 0; $i < 4; $i++) {
            $option_text = $options[$optionIndex];
            $is_correct = ($correctAnswers[$qIndex] == $i) ? 1 : 0;

            $stmt = $con->prepare("INSERT INTO dv_quiz_options (`question_id`, `option_text`, `is_correct`, `updated_at`) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param('isi', $question_id, $option_text, $is_correct);
            $stmt->execute();
            $stmt->close();

            $optionIndex++;
        }
    }

    $statusAction="Successful";
    $statusMessage="Questions added to quiz successfully!";
    showSuccessModal($statusAction,$statusMessage);
    //header("refresh:1; url=quizzes.php");
}


//add game
if (isset($_POST['addgame'])) {

   
    $incomplete_code = $_POST['incomplete_code'];
    $expected_output = $_POST['expected_output'];
    $points = $_POST['points'];
    $title = mysqli_real_escape_string($con, $_POST['title']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $course = $_POST['course'];

    if (!empty($course)) {
        $query = "SELECT l.s FROM " . $siteprefix . "courses c 
                  LEFT JOIN " . $siteprefix . "languages l ON c.language = l.s
                  WHERE c.s = '$course' LIMIT 1";
        $result = mysqli_query($con, $query);
        if ($result && $row = mysqli_fetch_assoc($result)) {
            $language = $row['s'];
        } else {
            $language = 1; // Set a default language ID
        }
    }

    // Get the last task number for this course
    $query = "SELECT level FROM dv_game_tasks WHERE course_id = ? ORDER BY level DESC LIMIT 1";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $course);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $level = $row['level'] + 1;
    } else {
        $level = 1;
    }
    $stmt->close();

    $stmt = $con->prepare("INSERT INTO `dv_game_tasks` (`s`, `level`, `language_id`, `course_id`, `title`, `incomplete_code`, `expected_output`, `points`, `created_date`, `created_by`, `description`) 
    VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?)");
    $stmt->bind_param("iiisssiis", $level, $language, $course, $title, $incomplete_code, $expected_output, $points, $user_id, $description);
    $stmt->execute();
    $stmt->close();
    

    $statusAction = "Successful";
    $statusMessage = "Game ($title) created successfully!";
    showSuccessModal($statusAction, $statusMessage);
    header("refresh:2; url=games.php");
}



//Handle form submission
if (isset($_POST['updatequiz'])) {
    $title = mysqli_real_escape_string($con, $_POST['title']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $course = $_POST['course'];
    $timer = $_POST['duration'];
    $points = $_POST['points'];
    $quiz_id = $_POST['quiz_id'];
    $language = getLanguagebyCourse($course);

    $query = "UPDATE dv_quiz SET 
              language_id = '$language',
              course_id = '$course',
              title = '$title',
              description = '$description',
              timer = '$timer',
              points = '$points',
              updated_at = NOW()
              WHERE s = '$quiz_id'";

    $submit = mysqli_query($con, $query);
    if ($submit) {
        $statusAction = "Successful";
        $statusMessage = "Quiz updated successfully!";
        showSuccessModal($statusAction, $statusMessage);
        header("refresh:1; url=quizzes.php");
    } else {
        $statusAction = "Oops!";
        $statusMessage = "An error has occurred!";
        showErrorModal($statusAction, $statusMessage);
    }
}


     // update game
     if (isset($_POST['updategame'])) {
        $task_id = $_POST['task_id'];
        $incomplete_code = $_POST['incomplete_code'];
        $expected_output = $_POST['expected_output'];
        $points = $_POST['points'];
        $level = $_POST['level'];
        $title = mysqli_real_escape_string($con, $_POST['title']);
        $description = mysqli_real_escape_string($con, $_POST['description']);
        $course = $_POST['course'];

        if (!empty($course)) {
            $query = "SELECT l.s FROM " . $siteprefix . "courses c 
                      LEFT JOIN " . $siteprefix . "languages l ON c.language = l.s
                      WHERE c.s = '$course' LIMIT 1";
            $result = mysqli_query($con, $query);
            if ($result && $row = mysqli_fetch_assoc($result)) {
                $language = $row['s'];
            } else {
                $language = 1; // Set a default language ID
            }
        }

        
        $stmt = $con->prepare("UPDATE `" . $siteprefix . "game_tasks` 
        SET language_id = ?, 
            course_id = ?, 
            title = ?, 
            level = ?,
            incomplete_code = ?, 
            expected_output = ?, 
            points = ?, 
            description = ?,
            created_date = NOW(), 
            created_by = ? 
        WHERE s = ?");

      if (!$stmt) { die("Error in prepare statement: " . $con->error); }

$stmt->bind_param("iisissssii", $language, $course, $title, $level, $incomplete_code, $expected_output, $points, $description, $user_id, $task_id);
$stmt->execute();
$stmt->close();


        $statusAction = "Successful";
        $statusMessage = "Game ($title) updated successfully!";
        showSuccessModal($statusAction, $statusMessage);
        header("refresh:2; url=games.php");
    }



    if(isset($_POST['settings'])){
        $name = $_POST['site_name'];
        $keywords = $_POST['site_keywords'];
        $url = $_POST['site_url']; 
        $description = $_POST['site_description'];
        $email = $_POST['site_mail'];
        $number = $_POST['site_number'];


        $uploadDir = '../../assets/img/';
        $fileKey='site_logo';
        global $fileName;
    
        // Update profile picture if a new one is uploaded
        if (!empty($profilePicture)) {
            $logo = handleFileUpload($fileKey, $uploadDir, $fileName);
        } else {
            $logo = $siteimg; // Use the current picture  
        }

      
        $update = mysqli_query($con,"UPDATE " . $siteprefix . "site_settings SET site_name='$name', site_keywords='$keywords', site_url='$url', site_description='$description', site_logo='$logo', site_mail='$email', site_number='$number' WHERE s=1");
    

        if($update){
         $statusAction = "Successful";
        $statusMessage = "Settings Updated Successfully!";
        showSuccessModal($statusAction, $statusMessage);
        header("refresh:2; url=settings.php");
        } else {
            $statusAction = "Oops!";
            $statusMessage = "An error has occurred!";
            showErrorModal($statusAction, $statusMessage);
        }
    }
?>






