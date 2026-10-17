const {
    Expense,
    Project
} = require("../models");

exports.index = async (req, res) => {
    try {
        const expenseRecords = await Expense.findAll({
            order: [["expense_id", "DESC"]]
        });

        const expenses = [];

        for (const record of expenseRecords) {
            const project = await Project.findByPk(record.project_id);

            expenses.push({
                expense_id: record.expense_id,
                project: project,
                category: record.category,
                amount: record.amount,
                description: record.description,
                expense_date: record.expense_date
            });
        }

        res.render("expenses/index", {
            user: req.user,
            expenses
        });
    } catch (error) {
        console.error("EXPENSE LIST ERROR:");
        console.error(error);
        res.status(500).send("Error loading expenses.");
    }
};

exports.showAddForm = async (req, res) => {
    try {
        const projects = await Project.findAll({
            order: [["project_id", "DESC"]]
        });

        res.render("expenses/add", {
            user: req.user,
            projects,
            error: null
        });
    } catch (error) {
        console.error("EXPENSE ADD FORM ERROR:");
        console.error(error);
        res.status(500).send("Error loading expense form.");
    }
};

exports.create = async (req, res) => {
    try {
        const {
            project_id,
            category,
            amount,
            description,
            expense_date
        } = req.body;

        await Expense.create({
            project_id: Number(project_id),
            recorded_by: req.user.user_id,
            category: category,
            amount: Number(amount || 0),
            description: description,
            expense_date: expense_date
        });

        res.redirect("/expenses");
    } catch (error) {
        console.error("EXPENSE CREATE ERROR:");
        console.error(error);
        res.status(500).send(
            "Error creating expense: " + error.message
        );
    }
};

exports.showEditForm = async (req, res) => {
    try {
        const expense = await Expense.findByPk(req.params.id);

        if (!expense) {
            return res.status(404).send("Expense not found.");
        }

        const projects = await Project.findAll({
            order: [["project_id", "DESC"]]
        });

        res.render("expenses/edit", {
            user: req.user,
            expense,
            projects,
            error: null
        });
    } catch (error) {
        console.error("EXPENSE EDIT FORM ERROR:");
        console.error(error);
        res.status(500).send("Error loading expense.");
    }
};

exports.update = async (req, res) => {
    try {
        const expense = await Expense.findByPk(req.params.id);

        if (!expense) {
            return res.status(404).send("Expense not found.");
        }

        const {
            project_id,
            category,
            amount,
            description,
            expense_date
        } = req.body;

        await expense.update({
            project_id: Number(project_id),
            category: category,
            amount: Number(amount || 0),
            description: description,
            expense_date: expense_date
        });

        res.redirect("/expenses");
    } catch (error) {
        console.error("EXPENSE UPDATE ERROR:");
        console.error(error);
        res.status(500).send(
            "Error updating expense: " + error.message
        );
    }
};

exports.delete = async (req, res) => {
    try {
        await Expense.destroy({
            where: {
                expense_id: req.params.id
            }
        });

        res.redirect("/expenses");
    } catch (error) {
        console.error("EXPENSE DELETE ERROR:");
        console.error(error);
        res.status(500).send("Error deleting expense.");
    }
};