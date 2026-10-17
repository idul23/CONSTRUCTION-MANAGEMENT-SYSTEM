const User = require("./User");
const Role = require("./Role");
const Project = require("./Project");
const Worker = require("./Worker");
const Client = require("./Client");
const Task = require("./Task");
const Material = require("./Material");
const Supplier = require("./Supplier");
const Skill = require("./Skill");
const ProjectPhase = require("./ProjectPhase");
const ProjectMember = require("./ProjectMember");
const WorkerSkill = require("./WorkerSkill");
const TaskAssignment = require("./TaskAssignment");
const Attendance = require("./Attendance");
const MaterialUsage = require("./MaterialUsage");
const Equipment = require("./Equipment");
const EquipmentUsage = require("./EquipmentUsage");
const Expense = require("./Expense");
const UserProfile = require("./UserProfile");
const AuditLog = require("./AuditLog");

Role.hasMany(User, {
    foreignKey: "role_id"
});

User.belongsTo(Role, {
    foreignKey: "role_id"
});

User.hasOne(UserProfile, {
    foreignKey: "user_id"
});

UserProfile.belongsTo(User, {
    foreignKey: "user_id"
});

Client.hasMany(Project, {
    foreignKey: "client_id"
});

Project.belongsTo(Client, {
    foreignKey: "client_id"
});

User.hasMany(Project, {
    foreignKey: "manager_id",
    as: "managedProjects"
});

Project.belongsTo(User, {
    foreignKey: "manager_id",
    as: "manager"
});

Project.hasMany(ProjectPhase, {
    foreignKey: "project_id"
});

ProjectPhase.belongsTo(Project, {
    foreignKey: "project_id"
});

ProjectPhase.hasMany(Task, {
    foreignKey: "phase_id"
});

Task.belongsTo(ProjectPhase, {
    foreignKey: "phase_id"
});

Project.hasMany(ProjectMember, {
    foreignKey: "project_id"
});

ProjectMember.belongsTo(Project, {
    foreignKey: "project_id"
});

User.hasMany(ProjectMember, {
    foreignKey: "user_id"
});

ProjectMember.belongsTo(User, {
    foreignKey: "user_id"
});

Worker.hasMany(WorkerSkill, {
    foreignKey: "worker_id"
});

WorkerSkill.belongsTo(Worker, {
    foreignKey: "worker_id"
});

Skill.hasMany(WorkerSkill, {
    foreignKey: "skill_id"
});

WorkerSkill.belongsTo(Skill, {
    foreignKey: "skill_id"
});

Task.hasMany(TaskAssignment, {
    foreignKey: "task_id"
});

TaskAssignment.belongsTo(Task, {
    foreignKey: "task_id"
});

Worker.hasMany(TaskAssignment, {
    foreignKey: "worker_id"
});

TaskAssignment.belongsTo(Worker, {
    foreignKey: "worker_id"
});

User.hasMany(TaskAssignment, {
    foreignKey: "assigned_by",
    as: "assignmentsCreated"
});

TaskAssignment.belongsTo(User, {
    foreignKey: "assigned_by",
    as: "assignedByUser"
});

TaskAssignment.hasMany(Attendance, {
    foreignKey: "assignment_id"
});

Attendance.belongsTo(TaskAssignment, {
    foreignKey: "assignment_id"
});

User.hasMany(Attendance, {
    foreignKey: "recorded_by",
    as: "attendanceRecords"
});

Attendance.belongsTo(User, {
    foreignKey: "recorded_by",
    as: "attendanceRecorder"
});

Supplier.hasMany(Material, {
    foreignKey: "supplier_id"
});

Material.belongsTo(Supplier, {
    foreignKey: "supplier_id"
});

Task.hasMany(MaterialUsage, {
    foreignKey: "task_id"
});

MaterialUsage.belongsTo(Task, {
    foreignKey: "task_id"
});

Material.hasMany(MaterialUsage, {
    foreignKey: "material_id"
});

MaterialUsage.belongsTo(Material, {
    foreignKey: "material_id"
});

User.hasMany(MaterialUsage, {
    foreignKey: "recorded_by",
    as: "materialUsageRecords"
});

MaterialUsage.belongsTo(User, {
    foreignKey: "recorded_by",
    as: "materialUsageRecorder"
});

Task.hasMany(EquipmentUsage, {
    foreignKey: "task_id"
});

EquipmentUsage.belongsTo(Task, {
    foreignKey: "task_id"
});

Equipment.hasMany(EquipmentUsage, {
    foreignKey: "equipment_id"
});

EquipmentUsage.belongsTo(Equipment, {
    foreignKey: "equipment_id"
});

User.hasMany(EquipmentUsage, {
    foreignKey: "recorded_by",
    as: "equipmentUsageRecords"
});

EquipmentUsage.belongsTo(User, {
    foreignKey: "recorded_by",
    as: "equipmentUsageRecorder"
});

Project.hasMany(Expense, {
    foreignKey: "project_id"
});

Expense.belongsTo(Project, {
    foreignKey: "project_id"
});

User.hasMany(Expense, {
    foreignKey: "recorded_by",
    as: "expenseRecords"
});

Expense.belongsTo(User, {
    foreignKey: "recorded_by",
    as: "expenseRecorder"
});

User.hasMany(AuditLog, {
    foreignKey: "changed_by",
    as: "auditLogs"
});

AuditLog.belongsTo(User, {
    foreignKey: "changed_by",
    as: "changedByUser"
});

module.exports = {
    User,
    Role,
    Project,
    Worker,
    Client,
    Task,
    Material,
    Supplier,
    Skill,
    ProjectPhase,
    ProjectMember,
    WorkerSkill,
    TaskAssignment,
    Attendance,
    MaterialUsage,
    Equipment,
    EquipmentUsage,
    Expense,
    UserProfile,
    AuditLog
};