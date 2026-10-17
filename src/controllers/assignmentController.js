const {
    TaskAssignment,
    Task,
    Worker,
    User
} = require("../models");


exports.index = async (req, res) => {
    try {

        const assignments = await TaskAssignment.findAll({
            include: [
                {
                    model: Task
                },
                {
                    model: Worker
                },
                {
                    model: User,
                    as: "assignedByUser"
                }
            ],
            order: [
                ["assignment_id", "DESC"]
            ]
        });


        res.render("assignments/index", {
            user: req.user,
            assignments
        });


    } catch (error) {

        console.error(
            "ASSIGNMENT LIST ERROR:"
        );

        console.error(error);


        res.status(500).send(
            "Error loading task assignments."
        );
    }
};


exports.showAddForm = async (req, res) => {
    try {

        const tasks = await Task.findAll({
            order: [
                ["task_id", "DESC"]
            ]
        });


        const workers = await Worker.findAll({
            where: {
                is_active: true
            },
            order: [
                ["first_name", "ASC"]
            ]
        });


        res.render("assignments/add", {
            user: req.user,
            tasks,
            workers,
            error: null
        });


    } catch (error) {

        console.error(
            "ASSIGNMENT ADD FORM ERROR:"
        );

        console.error(error);


        res.status(500).send(
            "Error loading assignment form."
        );
    }
};


exports.create = async (req, res) => {
    try {

        const {
            task_id,
            worker_id,
            assigned_role,
            progress_pct,
            status
        } = req.body;


        await TaskAssignment.create({

            task_id:
                Number(task_id),

            worker_id:
                Number(worker_id),

            assigned_by:
                req.user.user_id,

            assigned_role:
                assigned_role || null,

            assigned_at:
                new Date(),

            progress_pct:
                Number(progress_pct || 0),

            status:
                status || "assigned"

        });


        res.redirect(
            "/assignments"
        );


    } catch (error) {

        console.error(
            "ASSIGNMENT CREATE ERROR:"
        );

        console.error(error);


        try {

            const tasks =
                await Task.findAll({
                    order: [
                        ["task_id", "DESC"]
                    ]
                });


            const workers =
                await Worker.findAll({
                    where: {
                        is_active: true
                    },
                    order: [
                        ["first_name", "ASC"]
                    ]
                });


            res.render("assignments/add", {
                user: req.user,
                tasks,
                workers,
                error: error.message
            });


        } catch (secondError) {

            console.error(
                secondError
            );

            res.status(500).send(
                "Error creating task assignment."
            );
        }
    }
};


exports.showEditForm = async (req, res) => {
    try {

        const assignment =
            await TaskAssignment.findByPk(
                req.params.id
            );


        if (!assignment) {

            return res.status(404).send(
                "Task assignment not found."
            );
        }


        const tasks =
            await Task.findAll({
                order: [
                    ["task_id", "DESC"]
                ]
            });


        const workers =
            await Worker.findAll({
                order: [
                    ["first_name", "ASC"]
                ]
            });


        res.render("assignments/edit", {
            user: req.user,
            assignment,
            tasks,
            workers,
            error: null
        });


    } catch (error) {

        console.error(
            "ASSIGNMENT EDIT FORM ERROR:"
        );

        console.error(error);


        res.status(500).send(
            "Error loading assignment."
        );
    }
};


exports.update = async (req, res) => {
    try {

        const assignment =
            await TaskAssignment.findByPk(
                req.params.id
            );


        if (!assignment) {

            return res.status(404).send(
                "Task assignment not found."
            );
        }


        const {
            task_id,
            worker_id,
            assigned_role,
            progress_pct,
            status
        } = req.body;


        await assignment.update({

            task_id:
                Number(task_id),

            worker_id:
                Number(worker_id),

            assigned_role:
                assigned_role || null,

            progress_pct:
                Number(progress_pct || 0),

            status:
                status

        });


        res.redirect(
            "/assignments"
        );


    } catch (error) {

        console.error(
            "ASSIGNMENT UPDATE ERROR:"
        );

        console.error(error);


        res.status(500).send(
            "Error updating task assignment."
        );
    }
};


exports.delete = async (req, res) => {
    try {

        await TaskAssignment.destroy({
            where: {
                assignment_id:
                    req.params.id
            }
        });


        res.redirect(
            "/assignments"
        );


    } catch (error) {

        console.error(
            "ASSIGNMENT DELETE ERROR:"
        );

        console.error(error);


        res.status(500).send(
            "Error deleting task assignment."
        );
    }
};