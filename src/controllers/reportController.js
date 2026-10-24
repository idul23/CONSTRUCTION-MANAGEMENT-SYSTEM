const {
    Project,
    Worker,
    Task,
    Expense,
    Attendance,
    Material,
    Equipment,
    MaterialUsage,
    EquipmentUsage
} = require("../models");

exports.index = async (req, res) => {
    try {
        const [
            projects,
            workers,
            tasks,
            expenses,
            attendance,
            materials,
            equipment,
            materialUsage,
            equipmentUsage
        ] = await Promise.all([
            Project.findAll(),
            Worker.findAll(),
            Task.findAll(),
            Expense.findAll(),
            Attendance.findAll(),
            Material.findAll(),
            Equipment.findAll(),
            MaterialUsage.findAll(),
            EquipmentUsage.findAll()
        ]);

        const totalExpenses = expenses.reduce(
            (sum, item) => sum + Number(item.amount || item.expense_amount || item.cost || 0),
            0
        );

        const totalLaborCost = attendance.reduce(
            (sum, item) => sum + Number(item.labor_cost || 0),
            0
        );

        const totalMaterialUsageCost = materialUsage.reduce(
            (sum, item) => sum + Number(item.cost_snapshot || 0),
            0
        );

        const totalEquipmentUsageCost = equipmentUsage.reduce(
            (sum, item) => sum + Number(item.cost_snapshot || 0),
            0
        );

        res.render("reports/index", {
            user: req.user,
            projects,
            workers,
            tasks,
            expenses,
            attendance,
            materials,
            equipment,
            materialUsage,
            equipmentUsage,
            totalExpenses,
            totalLaborCost,
            totalMaterialUsageCost,
            totalEquipmentUsageCost
        });
    } catch (error) {
        console.error("Report controller error:", error);
        res.status(500).send("Unable to load reports. Check the terminal.");
    }
};