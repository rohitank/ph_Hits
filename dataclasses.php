<?php

    class Content
    {
        public int $contentID;
        public string $contentTitle;
        public string $contentDesc;
        public string $schedDate;
        public int $userID;
        public ?string $filePath;

        public function __construct($contentID, $contentTitle, $contentDesc, $schedDate, $userID, $filePath)
        {
            $this->contentID = $contentID;
            $this->contentTitle = $contentTitle;
            $this->contentDesc = $contentDesc;
            $this->schedDate = $schedDate;
            $this->userID = $userID;
            $this->filePath = $filePath;
        }
    }

 
    class History
    {
        public int $historyID;
        public string $streamDate;
        public string $histFilePath;

        public function __construct($historyID, $streamDate, $histFilePath)
        {
            $this->historyID = $historyID;
            $this->streamDate = $streamDate;
            $this->histFilePath = $histFilePath;
        }
    }

 
    class Queue
    {
        public int $queueNum;
        public int $contentID;
        public string $dateOfAiring;

        public function __construct($queueNum, $contentID, $dateOfAiring)
        {
            $this->queueNum = $queueNum;
            $this->contentID = $contentID;
            $this->dateOfAiring = $dateOfAiring;
        }
    }

   
    class Users
    {
        public int $userID;
        public string $fName;
        public string $lName;
        public string $userName;
        public string $password;
        public ?string $status;
        public ?string $role;

        public function __construct($userID, $fName, $lName, $userName, $password, $status, $role)
        {
            $this->userID = $userID;
            $this->fName = $fName;
            $this->lName = $lName;
            $this->userName = $userName;
            $this->password = $password;
            $this->status = $status;
            $this->role = $role;
        }
}