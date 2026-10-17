const {
    Project,
    Worker,
    Task,
    Material,
    Expense,
    Attendance
} = require("../models");

exports.index = async (req, res) => {
    try {
        const projects = await Project.findAll({
            order: [["project_id", "DESC"]]
        });

        const workers = await Worker.findAll({
            order: [["worker_id", "DESC"]]
        });

        const tasks = await Task.findAll({
            order: [["task_id", "DESC"]]
        });

        const materials = await Material.findAll({
            order: [["material_id", "DESC"]]
        });

        const expenses = await Expense.findAll({
            order: [["expense_id", "DESC"]]
        });

        const attendance = await Attendance.findAll({
            order: [["attendance_id", "DESC"]]
        });

        let totalExpenses = 0;

        expenses.forEach(expense => {
            totalExpenses += Number(expense.amount || 0);
        });

        let totalLaborCost = 0;

        attendance.forEach(record => {
            totalLaborCost += Number(record.labor_cost || 0);
        });

        res.render("reports/index", {
            user: req.user,
            projects,
            workers,
            tasks,
            materials,
            expenses,
            attendance,
            totalExpenses,
            totalLaborCost
        });

    } catch (error) {
        console.error("REPORT ERROR:");
        console.error(error);

        res.status(500).send("Error loading reports.");
    }
};