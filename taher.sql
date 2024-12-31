-- =======================================
-- 1) إنشاء قاعدة بيانات (إن لم توجد)
-- =======================================
CREATE DATABASE IF NOT EXISTS MyHRDatabase
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_general_ci;

USE MyHRDatabase;

-- =======================================
-- 2) حذف الجداول إن وُجدت مسبقًا
-- بالترتيب العكسي (من الأحدث إلى الأقدم)
-- =======================================
DROP TABLE IF EXISTS ExecutedTraining;
DROP TABLE IF EXISTS JobGrades;
DROP TABLE IF EXISTS EmployeeNumbers;
DROP TABLE IF EXISTS JobStatusSequences;
DROP TABLE IF EXISTS TrainingPackagePrograms;
DROP TABLE IF EXISTS TrainingPackages;
DROP TABLE IF EXISTS TrainingRequests;
DROP TABLE IF EXISTS TrainingNeeds;
DROP TABLE IF EXISTS PeerEvaluationResponses;
DROP TABLE IF EXISTS ManagerEvaluationResponses;
DROP TABLE IF EXISTS SelfEvaluationResponses;
DROP TABLE IF EXISTS EvaluationQuestions;
DROP TABLE IF EXISTS EvaluationCriteria;
DROP TABLE IF EXISTS JobMethods;
DROP TABLE IF EXISTS AchievementTypes;
DROP TABLE IF EXISTS Achievements;
DROP TABLE IF EXISTS Leaves;
DROP TABLE IF EXISTS Absences;
DROP TABLE IF EXISTS Actions;
DROP TABLE IF EXISTS Warnings;
DROP TABLE IF EXISTS Evaluations;
DROP TABLE IF EXISTS TrainingAttendance;
DROP TABLE IF EXISTS TrainingPrograms;
DROP TABLE IF EXISTS RolePermissions;
DROP TABLE IF EXISTS Employees;
DROP TABLE IF EXISTS Jobs;
DROP TABLE IF EXISTS Sections;
DROP TABLE IF EXISTS Users;
DROP TABLE IF EXISTS Departments;
DROP TABLE IF EXISTS Permissions;
DROP TABLE IF EXISTS Roles;
DROP TABLE IF EXISTS Qualifications;
DROP TABLE IF EXISTS JobTypes;
DROP TABLE IF EXISTS Nationalities;


-- =======================================
-- 3) إنشاء الجداول بالترتيب الصحيح
-- =======================================

-- (1) جدول الجنسيات
CREATE TABLE Nationalities (
    NationalityID   INT PRIMARY KEY,
    NationalityName VARCHAR(50)
) ENGINE=InnoDB;

-- (2) جدول أنواع الوظائف
CREATE TABLE JobTypes (
    JobTypeID   INT PRIMARY KEY,
    JobTypeName VARCHAR(50)
) ENGINE=InnoDB;

-- (3) جدول المؤهلات العلمية
CREATE TABLE Qualifications (
    QualificationID   INT PRIMARY KEY,
    QualificationName VARCHAR(50)
) ENGINE=InnoDB;

-- (4) جدول الأدوار
CREATE TABLE Roles (
    RoleID   INT PRIMARY KEY,
    RoleName VARCHAR(50)
) ENGINE=InnoDB;

-- (5) جدول الصلاحيات
CREATE TABLE Permissions (
    PermissionID   INT PRIMARY KEY,
    PermissionName VARCHAR(50),
    Description    TEXT,
    PermissionStatus VARCHAR(10)
) ENGINE=InnoDB;

-- (6) جدول الإدارات
CREATE TABLE Departments (
    DepartmentID   INT PRIMARY KEY,
    DepartmentName VARCHAR(50),
    ManagerID      INT
) ENGINE=InnoDB;

-- (7) جدول المستخدمين
CREATE TABLE Users (
    UserID       INT PRIMARY KEY,
    Username     VARCHAR(10),
    PasswordHash VARCHAR(180),
    RoleID       INT,
    Email        VARCHAR(20),
    PhoneNumber  INT,
    AccountStatus VARCHAR(10),
    CONSTRAINT fk_users_roles
      FOREIGN KEY (RoleID)
      REFERENCES Roles(RoleID)
      ON UPDATE CASCADE
      ON DELETE SET NULL
) ENGINE=InnoDB;

-- (8) جدول الأقسام
CREATE TABLE Sections (
    SectionID    INT PRIMARY KEY,
    DepartmentID INT,
    SectionName  VARCHAR(50),
    CONSTRAINT fk_sections_dept
      FOREIGN KEY (DepartmentID)
      REFERENCES Departments(DepartmentID)
      ON UPDATE CASCADE
      ON DELETE SET NULL
) ENGINE=InnoDB;

-- (9) جدول الوظائف
CREATE TABLE Jobs (
    JobID    INT PRIMARY KEY,
    JobTitle VARCHAR(50),
    JobTypeID INT,
    CONSTRAINT fk_jobs_jobtype
      FOREIGN KEY (JobTypeID)
      REFERENCES JobTypes(JobTypeID)
      ON UPDATE CASCADE
      ON DELETE SET NULL
) ENGINE=InnoDB;

-- (10) جدول الموظفين
CREATE TABLE Employees (
    EmployeeID      INT PRIMARY KEY,
    UserID          INT,
    FirstName       VARCHAR(20),
    MiddleName      VARCHAR(20),
    LastName        VARCHAR(20),
    DepartmentID    INT,
    NationalityID   INT,
    Gender          VARCHAR(6),
    BirthDate       DATE,
    QualificationID INT,
    JobID           INT,
    CONSTRAINT fk_emp_user
      FOREIGN KEY (UserID)
      REFERENCES Users(UserID)
      ON UPDATE CASCADE
      ON DELETE SET NULL,
    CONSTRAINT fk_emp_dept
      FOREIGN KEY (DepartmentID)
      REFERENCES Departments(DepartmentID)
      ON UPDATE CASCADE
      ON DELETE SET NULL,
    CONSTRAINT fk_emp_nat
      FOREIGN KEY (NationalityID)
      REFERENCES Nationalities(NationalityID)
      ON UPDATE CASCADE
      ON DELETE SET NULL,
    CONSTRAINT fk_emp_qual
      FOREIGN KEY (QualificationID)
      REFERENCES Qualifications(QualificationID)
      ON UPDATE CASCADE
      ON DELETE SET NULL,
    CONSTRAINT fk_emp_job
      FOREIGN KEY (JobID)
      REFERENCES Jobs(JobID)
      ON UPDATE CASCADE
      ON DELETE SET NULL
) ENGINE=InnoDB;

-- (11) جدول ربط الأدوار بالصلاحيات
CREATE TABLE RolePermissions (
    RolePermissionID INT PRIMARY KEY,
    RoleID           INT,
    PermissionID     INT,
    CONSTRAINT fk_rp_role
      FOREIGN KEY (RoleID)
      REFERENCES Roles(RoleID)
      ON UPDATE CASCADE
      ON DELETE CASCADE,
    CONSTRAINT fk_rp_perm
      FOREIGN KEY (PermissionID)
      REFERENCES Permissions(PermissionID)
      ON UPDATE CASCADE
      ON DELETE CASCADE
) ENGINE=InnoDB;

-- (12) جدول البرامج التدريبية
CREATE TABLE TrainingPrograms (
    TrainingID      INT PRIMARY KEY,
    TrainingName    VARCHAR(50),
    TrainingDuration INT,
    MaxAttendees    INT
) ENGINE=InnoDB;

-- (13) جدول حضور التدريب
CREATE TABLE TrainingAttendance (
    AttendanceID  INT PRIMARY KEY,
    EmployeeID    INT,
    TrainingID    INT,
    Status        VARCHAR(20),
    TrainingScore FLOAT,
    CONSTRAINT fk_att_emp
      FOREIGN KEY (EmployeeID)
      REFERENCES Employees(EmployeeID)
      ON UPDATE CASCADE
      ON DELETE CASCADE,
    CONSTRAINT fk_att_training
      FOREIGN KEY (TrainingID)
      REFERENCES TrainingPrograms(TrainingID)
      ON UPDATE CASCADE
      ON DELETE CASCADE
) ENGINE=InnoDB;

-- (14) جدول التقييمات
CREATE TABLE Evaluations (
    EvaluationID INT PRIMARY KEY,
    EvaluatorID  INT,
    EvaluateeID  INT,
    ManagerScore FLOAT,
    PeerScore    FLOAT,
    Feedback     TEXT,
    -- نفترض بأن المقيمين والموظف المُقيّم كلاهما في Employees
    CONSTRAINT fk_eval_evaluator
      FOREIGN KEY (EvaluatorID)
      REFERENCES Employees(EmployeeID)
      ON UPDATE CASCADE
      ON DELETE SET NULL,
    CONSTRAINT fk_eval_evaluatee
      FOREIGN KEY (EvaluateeID)
      REFERENCES Employees(EmployeeID)
      ON UPDATE CASCADE
      ON DELETE SET NULL
) ENGINE=InnoDB;

-- (15) جدول إنذارات الموظفين
CREATE TABLE Warnings (
    WarningID      INT PRIMARY KEY,
    EmployeeID     INT,
    WarningMessage TEXT,
    WarningDate    DATE,
    WarningSource  VARCHAR(255),
    CONSTRAINT fk_warnings_emp
      FOREIGN KEY (EmployeeID)
      REFERENCES Employees(EmployeeID)
      ON UPDATE CASCADE
      ON DELETE CASCADE
) ENGINE=InnoDB;

-- (16) جدول العقوبات
CREATE TABLE Actions (
    ActionID      INT PRIMARY KEY,
    EmployeeID    INT,
    ActionType    VARCHAR(100),
    ActionDate    DATE,
    ViolationType VARCHAR(255),
    CONSTRAINT fk_actions_emp
      FOREIGN KEY (EmployeeID)
      REFERENCES Employees(EmployeeID)
      ON UPDATE CASCADE
      ON DELETE CASCADE
) ENGINE=InnoDB;

-- (17) جدول الغياب
CREATE TABLE Absences (
    AbsenceID   INT PRIMARY KEY,
    EmployeeID  INT,
    AbsenceDays INT,
    AbsenceYear INT,
    CONSTRAINT fk_absences_emp
      FOREIGN KEY (EmployeeID)
      REFERENCES Employees(EmployeeID)
      ON UPDATE CASCADE
      ON DELETE CASCADE
) ENGINE=InnoDB;

-- (18) جدول الإجازات
CREATE TABLE Leaves (
    LeaveID       INT PRIMARY KEY,
    EmployeeID    INT,
    LeaveReason   TEXT,
    StartDate     DATE,
    EndDate       DATE,
    LeaveDuration INT,
    CONSTRAINT fk_leaves_emp
      FOREIGN KEY (EmployeeID)
      REFERENCES Employees(EmployeeID)
      ON UPDATE CASCADE
      ON DELETE CASCADE
) ENGINE=InnoDB;

-- (19) جدول الإنجازات
CREATE TABLE Achievements (
    AchievementID INT PRIMARY KEY,
    EmployeeID    INT,
    Description   TEXT,
    DateAchieved  DATE,
    CONSTRAINT fk_achievements_emp
      FOREIGN KEY (EmployeeID)
      REFERENCES Employees(EmployeeID)
      ON UPDATE CASCADE
      ON DELETE CASCADE
) ENGINE=InnoDB;

-- (20) جدول أنواع الإنجازات
CREATE TABLE AchievementTypes (
    AchievementTypeID INT PRIMARY KEY,
    AchievementName   VARCHAR(50)
) ENGINE=InnoDB;

-- (21) جدول طريقة شغل الوظائف
CREATE TABLE JobMethods (
    JobMethodID          INT PRIMARY KEY,
    JobMethodDescription VARCHAR(50)
) ENGINE=InnoDB;

-- (22) جدول معايير التقييم
CREATE TABLE EvaluationCriteria (
    CriteriaID   INT PRIMARY KEY,
    CriteriaName VARCHAR(50),
    Minimum      INT,
    Maximum      INT,
    Weight       FLOAT
) ENGINE=InnoDB;

-- (23) جدول الأسئلة المرتبطة بالتقييم
CREATE TABLE EvaluationQuestions (
    QuestionID     INT PRIMARY KEY,
    QuestionText   TEXT,
    CriteriaID     INT,
    QuestionWeight INT,
    CONSTRAINT fk_evalquestions_criteria
      FOREIGN KEY (CriteriaID)
      REFERENCES EvaluationCriteria(CriteriaID)
      ON UPDATE CASCADE
      ON DELETE CASCADE
) ENGINE=InnoDB;

-- (24) جدول إجابات التقييم الذاتي
CREATE TABLE SelfEvaluationResponses (
    ResponseID   INT PRIMARY KEY,
    EmployeeID   INT,
    EvaluationID INT,
    QuestionID   INT,
    Answer       VARCHAR(255),
    Notes        TEXT,
    CONSTRAINT fk_selfeval_emp
      FOREIGN KEY (EmployeeID)
      REFERENCES Employees(EmployeeID)
      ON UPDATE CASCADE
      ON DELETE CASCADE,
    CONSTRAINT fk_selfeval_evaluation
      FOREIGN KEY (EvaluationID)
      REFERENCES Evaluations(EvaluationID)
      ON UPDATE CASCADE
      ON DELETE CASCADE,
    CONSTRAINT fk_selfeval_question
      FOREIGN KEY (QuestionID)
      REFERENCES EvaluationQuestions(QuestionID)
      ON UPDATE CASCADE
      ON DELETE CASCADE
) ENGINE=InnoDB;

-- (25) جدول إجابات تقييم المدير
CREATE TABLE ManagerEvaluationResponses (
    ResponseID     INT PRIMARY KEY,
    EvaluatorID    INT,
    EvaluationID   INT,
    QuestionID     INT,
    Answer         VARCHAR(255),
    Notes          TEXT,
    Recommendation VARCHAR(255),
    CONSTRAINT fk_managereval_evaluator
      FOREIGN KEY (EvaluatorID)
      REFERENCES Employees(EmployeeID)
      ON UPDATE CASCADE
      ON DELETE SET NULL,
    CONSTRAINT fk_managereval_eval
      FOREIGN KEY (EvaluationID)
      REFERENCES Evaluations(EvaluationID)
      ON UPDATE CASCADE
      ON DELETE CASCADE,
    CONSTRAINT fk_managereval_question
      FOREIGN KEY (QuestionID)
      REFERENCES EvaluationQuestions(QuestionID)
      ON UPDATE CASCADE
      ON DELETE CASCADE
) ENGINE=InnoDB;

-- (26) جدول إجابات تقييم الزملاء
CREATE TABLE PeerEvaluationResponses (
    ResponseID  INT PRIMARY KEY,
    PeerID      INT,
    EvaluateeID INT,
    QuestionID  INT,
    Answer      VARCHAR(255),
    Notes       TEXT,
    CONSTRAINT fk_peer_eval_peer
      FOREIGN KEY (PeerID)
      REFERENCES Employees(EmployeeID)
      ON UPDATE CASCADE
      ON DELETE CASCADE,
    CONSTRAINT fk_peer_eval_evaluatee
      FOREIGN KEY (EvaluateeID)
      REFERENCES Employees(EmployeeID)
      ON UPDATE CASCADE
      ON DELETE CASCADE,
    CONSTRAINT fk_peer_eval_question
      FOREIGN KEY (QuestionID)
      REFERENCES EvaluationQuestions(QuestionID)
      ON UPDATE CASCADE
      ON DELETE CASCADE
) ENGINE=InnoDB;

-- (27) جدول الاحتياجات التدريبية
CREATE TABLE TrainingNeeds (
    NeedID       INT PRIMARY KEY,
    EmployeeID   INT,
    TrainingType VARCHAR(50),
    RequestDate  DATE,
    CONSTRAINT fk_needs_emp
      FOREIGN KEY (EmployeeID)
      REFERENCES Employees(EmployeeID)
      ON UPDATE CASCADE
      ON DELETE CASCADE
) ENGINE=InnoDB;

-- (28) جدول طلبات الانضمام للبرامج التدريبية
CREATE TABLE TrainingRequests (
    RequestID    INT PRIMARY KEY,
    EmployeeID   INT,
    TrainingID   INT,
    RequestState VARCHAR(10),
    RequestDate  DATE,
    CONSTRAINT fk_trainingreq_emp
      FOREIGN KEY (EmployeeID)
      REFERENCES Employees(EmployeeID)
      ON UPDATE CASCADE
      ON DELETE CASCADE,
    CONSTRAINT fk_trainingreq_training
      FOREIGN KEY (TrainingID)
      REFERENCES TrainingPrograms(TrainingID)
      ON UPDATE CASCADE
      ON DELETE CASCADE
) ENGINE=InnoDB;

-- (29) جدول الحقائب التدريبية
CREATE TABLE TrainingPackages (
    PackageID          INT PRIMARY KEY,
    PackageName        VARCHAR(50),
    ImprovementFieldID INT   -- لم نرى جدولا باسم ImprovementFields في القائمة
) ENGINE=InnoDB;

-- (30) جدول ربط البرامج التدريبية بالحقائب
CREATE TABLE TrainingPackagePrograms (
    TrainingPackageProgramID INT PRIMARY KEY,
    PackageID                INT,
    TrainingID               INT,
    CONSTRAINT fk_tpp_package
      FOREIGN KEY (PackageID)
      REFERENCES TrainingPackages(PackageID)
      ON UPDATE CASCADE
      ON DELETE CASCADE,
    CONSTRAINT fk_tpp_training
      FOREIGN KEY (TrainingID)
      REFERENCES TrainingPrograms(TrainingID)
      ON UPDATE CASCADE
      ON DELETE CASCADE
) ENGINE=InnoDB;

-- (31) جدول تسلسل الحالة الوظيفية
CREATE TABLE JobStatusSequences (
    StatusSequenceID INT PRIMARY KEY,
    EmployeeID       INT,
    JobID            INT,
    DepartmentID     INT,
    StartDate        DATE,
    EndDate          DATE,
    CONSTRAINT fk_jobstatus_emp
      FOREIGN KEY (EmployeeID)
      REFERENCES Employees(EmployeeID)
      ON UPDATE CASCADE
      ON DELETE CASCADE,
    CONSTRAINT fk_jobstatus_job
      FOREIGN KEY (JobID)
      REFERENCES Jobs(JobID)
      ON UPDATE CASCADE
      ON DELETE CASCADE,
    CONSTRAINT fk_jobstatus_dept
      FOREIGN KEY (DepartmentID)
      REFERENCES Departments(DepartmentID)
      ON UPDATE CASCADE
      ON DELETE CASCADE
) ENGINE=InnoDB;

-- (32) جدول بيانات أرقام الموظفين
CREATE TABLE EmployeeNumbers (
    EmployeeNumberID INT PRIMARY KEY,
    EmployeeID       INT,
    EmployeeName     VARCHAR(50),
    CONSTRAINT fk_empnumbers_emp
      FOREIGN KEY (EmployeeID)
      REFERENCES Employees(EmployeeID)
      ON UPDATE CASCADE
      ON DELETE CASCADE
) ENGINE=InnoDB;

-- (33) جدول الدرجات الوظيفية
CREATE TABLE JobGrades (
    GradeID      INT PRIMARY KEY,
    EmployeeID   INT,
    GradeNumber  INT,
    ObtainedDate DATE,
    CONSTRAINT fk_jobgrades_emp
      FOREIGN KEY (EmployeeID)
      REFERENCES Employees(EmployeeID)
      ON UPDATE CASCADE
      ON DELETE CASCADE
) ENGINE=InnoDB;

-- (34) جدول برامج التدريب المنفذة
CREATE TABLE ExecutedTraining (
    ExecutedTrainingID INT PRIMARY KEY,
    EmployeeID         INT,
    TrainingID         INT,
    Status             VARCHAR(20),
    Score              FLOAT,
    CONSTRAINT fk_exec_train_emp
      FOREIGN KEY (EmployeeID)
      REFERENCES Employees(EmployeeID)
      ON UPDATE CASCADE
      ON DELETE CASCADE,
    CONSTRAINT fk_exec_train_program
      FOREIGN KEY (TrainingID)
      REFERENCES TrainingPrograms(TrainingID)
      ON UPDATE CASCADE
      ON DELETE CASCADE
) ENGINE=InnoDB;

-- =======================================
-- انتهى إنشاء الجداول بالمفاتيح
-- =======================================
