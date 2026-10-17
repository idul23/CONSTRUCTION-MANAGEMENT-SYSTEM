const { Project, Client, User } = require("../models");


exports.index = async (req, res) => {
    try {
        const projects = await Project.findAll({
            include: [
                {
                    model: Client
                },
                {
                    model: User,
                    as: "manager"
                }
            ],
            order: [["project_id", "DESC"]]
        });

        res.render("projects/index", {
            user: req.user,
            projects
        });

    } catch (error) {
        console.error(error);
        res.status(500).send("Error loading projects.");
    }
};


exports.showAddForm = async (req, res) => {
    try {
        const clients = await Client.findAll({
            order: [["client_name", "ASC"]]
        });

        const managers = await User.findAll({
            order: [["username", "ASC"]]
        });

        res.render("projects/add", {
            user: req.user,
            clients,
            managers,
            error: null
        });

    } catch (error) {
        console.error(error);
        res.status(500).send("Error loading add project page.");
    }
};


exports.create = async (req, res) => {
    try {
        const {
            client_id,
            manager_id,
            project_name,
            location,
            start_date,
            end_date,
            total_budget,
            status
        } = req.body;

        await Project.create({
            client_id,
            manager_id,
            project_name,
            location,
            start_date,
            end_date: end_date || null,
            total_budget,
            remaining_budget: total_budget,
            status
        });

        res.redirect("/projects");

    } catch (error) {
        console.error(error);

        try {
            const clients = await Client.findAll({
                order: [["client_name", "ASC"]]
            });

            const managers = await User.findAll({
                order: [["username", "ASC"]]
            });

            res.render("projects/add", {
                user: req.user,
                clients,
                managers,
                error: "Unable to create project. Please check your information."
            });

        } catch (secondError) {
            console.error(secondError);
            res.status(500).send("Error creating project.");
        }
    }
};


exports.showEditForm = async (req, res) => {
    try {
        const projectId = req.params.id;

        const project = await Project.findByPk(projectId);

        if (!project) {
            return res.status(404).send("Project not found.");
        }

        const clients = await Client.findAll({
            order: [["client_name", "ASC"]]
        });

        const managers = await User.findAll({
            order: [["username", "ASC"]]
        });

        res.render("projects/edit", {
            user: req.user,
            project,
            clients,
            managers,
            error: null
        });

    } catch (error) {
        console.error(error);
        res.status(500).send("Error loading edit project page.");
    }
};


exports.update = async (req, res) => {
    try {
        const projectId = req.params.id;

        const {
            client_id,
            manager_id,
            project_name,
            location,
            start_date,
            end_date,
            total_budget,
            status
        } = req.body;

        const project = await Project.findByPk(projectId);

        if (!project) {
            return res.status(404).send("Project not found.");
        }

        const oldBudget = Number(project.total_budget);
        const newBudget = Number(total_budget);

        const spentAmount = oldBudget - Number(project.remaining_budget);

        let newRemainingBudget = newBudget - spentAmount;

        if (newRemainingBudget < 0) {
            newRemainingBudget = 0;
        }

        await project.update({
            client_id,
            manager_id,
            project_name,
            location,
            start_date,
            end_date: end_date || null,
            total_budget: newBudget,
            remaining_budget: newRemainingBudget,
            status
        });

        res.redirect("/projects");

    } catch (error) {
        console.error(error);
        res.status(500).send("Error updating project.");
    }
};


exports.delete = async (req, res) => {
    try {
        const projectId = req.params.id;

        await Project.destroy({
            where: {
                project_id: projectId
            }
        });

        res.redirect("/projects");

    } catch (error) {
        console.error(error);
        res.status(500).send("Error deleting project.");
    }
};