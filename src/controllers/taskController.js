const {
    Task,
    ProjectPhase,
    Project
} = require("../models");


exports.index = async (req, res) => {
    try {

        const tasks = await Task.findAll({
            include: [
                {
                    model: ProjectPhase,
                    include: [
                        {
                            model: Project
                        }
                    ]
                }
            ],
            order: [
                ["task_id", "DESC"]
            ]
        });

        res.render("tasks/index", {
            user: req.user,
            tasks
        });

    } catch (error) {

        console.error("TASK LIST ERROR:");
        console.error(error);

        res.status(500).send(
            "Error loading tasks."
        );
    }
};


exports.showAddForm = async (req, res) => {
    try {

        const phases = await ProjectPhase.findAll({
            include: [
                {
                    model: Project
                }
            ],
            order: [
                ["phase_id", "DESC"]
            ]
        });

        res.render("tasks/add", {
            user: req.user,
            phases,
            error: null
        });

    } catch (error) {

        console.error("TASK ADD FORM ERROR:");
        console.error(error);

        res.status(500).send(
            "Error loading add task page."
        );
    }
};


exports.create = async (req, res) => {
    try {

        console.log("TASK FORM DATA:");
        console.log(req.body);

        const {
            phase_id,
            task_name,
            start_date,
            due_date,
            progress_pct,
            status
        } = req.body;


        await Task.create({
            phase_id: Number(phase_id),
            task_name: task_name,
            start_date: start_date,
            due_date: due_date,
            progress_pct: Number(progress_pct || 0),
            status: status
        });


        console.log("TASK CREATED SUCCESSFULLY.");

        res.redirect("/tasks");

    } catch (error) {

        console.error("TASK CREATE ERROR:");
        console.error(error);

        try {

            const phases = await ProjectPhase.findAll({
                include: [
                    {
                        model: Project
                    }
                ],
                order: [
                    ["phase_id", "DESC"]
                ]
            });

            res.render("tasks/add", {
                user: req.user,
                phases,
                error: error.message
            });

        } catch (secondError) {

            console.error(
                "TASK ERROR PAGE ERROR:"
            );

            console.error(secondError);

            res.status(500).send(
                "Error creating task."
            );
        }
    }
};


exports.showEditForm = async (req, res) => {
    try {

        const task = await Task.findByPk(
            req.params.id
        );

        if (!task) {
            return res.status(404).send(
                "Task not found."
            );
        }


        const phases = await ProjectPhase.findAll({
            include: [
                {
                    model: Project
                }
            ],
            order: [
                ["phase_id", "DESC"]
            ]
        });


        res.render("tasks/edit", {
            user: req.user,
            task,
            phases,
            error: null
        });

    } catch (error) {

        console.error("TASK EDIT FORM ERROR:");
        console.error(error);

        res.status(500).send(
            "Error loading edit task page."
        );
    }
};


exports.update = async (req, res) => {
    try {

        const task = await Task.findByPk(
            req.params.id
        );

        if (!task) {
            return res.status(404).send(
                "Task not found."
            );
        }


        const {
            phase_id,
            task_name,
            start_date,
            due_date,
            progress_pct,
            status
        } = req.body;


        await task.update({
            phase_id: Number(phase_id),
            task_name: task_name,
            start_date: start_date,
            due_date: due_date,
            progress_pct: Number(progress_pct || 0),
            status: status
        });


        res.redirect("/tasks");

    } catch (error) {

        console.error("TASK UPDATE ERROR:");
        console.error(error);

        res.status(500).send(
            "Error updating task."
        );
    }
};


exports.delete = async (req, res) => {
    try {

        await Task.destroy({
            where: {
                task_id: req.params.id
            }
        });

        res.redirect("/tasks");

    } catch (error) {

        console.error("TASK DELETE ERROR:");
        console.error(error);

        res.status(500).send(
            "Error deleting task."
        );
    }
};