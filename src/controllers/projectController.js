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
        console.error("ERROR LOADING PROJECTS:", error);
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
        console.error("ERROR LOADING ADD PROJECT FORM:", error);
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

        console.log("PROJECT FORM DATA:", req.body);

        if (
            !client_id ||
            !manager_id ||
            !project_name?.trim() ||
            !location?.trim() ||
            !start_date ||
            !total_budget ||
            !status
        ) {
            throw new Error("Please complete all required project fields.");
        }

        if (
            end_date &&
            new Date(end_date) < new Date(start_date)
        ) {
            throw new Error("End date cannot be earlier than start date.");
        }

        const client = await Client.findByPk(client_id);
        const manager = await User.findByPk(manager_id);

        if (!client) {
            throw new Error("Selected client does not exist.");
        }

        if (!manager) {
            throw new Error("Selected project manager does not exist.");
        }

        const budget = Number(total_budget);

        if (!Number.isFinite(budget) || budget <= 0) {
            throw new Error("Please enter a valid project budget.");
        }

        await Project.create({
            client_id,
            manager_id,
            project_name: project_name.trim(),
            location: location.trim(),
            start_date,
            end_date: end_date || null,
            total_budget: budget,
            remaining_budget: budget,
            status
        });

        res.redirect("/projects");

    } catch (error) {
        console.error("PROJECT CREATION ERROR:", error);
        console.error("DATABASE ERROR:", error.original || error.parent || "No database error details.");

        try {
            const clients = await Client.findAll({
                order: [["client_name", "ASC"]]
            });

            const managers = await User.findAll({
                order: [["username", "ASC"]]
            });

            res.status(400).render("projects/add", {
                user: req.user,
                clients,
                managers,
                error: error.message || "Unable to create project. Please check your information."
            });

        } catch (secondError) {
            console.error("FORM RELOAD ERROR:", secondError);
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
        console.error("ERROR LOADING EDIT PROJECT FORM:", error);
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
        console.error("ERROR UPDATING PROJECT:", error);
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
        console.error("ERROR DELETING PROJECT:", error);
        res.status(500).send("Error deleting project.");
    }
};