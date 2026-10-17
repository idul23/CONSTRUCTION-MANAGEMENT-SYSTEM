const { Project, Worker, Task, Material, Attendance, Expense } = require("../models");

exports.showDashboard = async (req, res) => {
    try {
        const totalProjects = await Project.count();
        const activeWorkers = await Worker.count({
            where: {
                is_active: true
            }
        });
        const totalTasks = await Task.count();
        const totalMaterials = await Material.count();

        const today = new Date().toISOString().split("T")[0];

        const todayAttendance = await Attendance.count({
            where: {
                work_date: today
            }
        });

        const expenseRecords = await Expense.findAll({
            attributes: ["amount"]
        });

        let totalExpenses = 0;

        expenseRecords.forEach(expense => {
            totalExpenses += Number(expense.amount);
        });

        const recentProjects = await Project.findAll({
            order: [["project_id", "DESC"]],
            limit: 5
        });

        const recentTasks = await Task.findAll({
            order: [["task_id", "DESC"]],
            limit: 5
        });

        res.render("dashboard", {
            user: req.user,
            totalProjects,
            activeWorkers,
            totalTasks,
            totalMaterials,
            todayAttendance,
            totalExpenses,
            recentProjects,
            recentTasks
        });

    } catch (error) {
        console.error(error);

        res.status(500).send("Error loading dashboard.");
    }
};