const { Worker } = require("../models");


exports.index = async (req, res) => {
    try {
        const workers = await Worker.findAll({
            order: [["worker_id", "DESC"]]
        });

        res.render("workers/index", {
            user: req.user,
            workers
        });

    } catch (error) {
        console.error(error);
        res.status(500).send("Error loading workers.");
    }
};


exports.showAddForm = (req, res) => {
    res.render("workers/add", {
        user: req.user,
        error: null
    });
};


exports.create = async (req, res) => {
    try {
        const {
            first_name,
            last_name,
            contact_no,
            hourly_rate,
            hire_date,
            is_active
        } = req.body;

        await Worker.create({
            first_name,
            last_name,
            contact_no: contact_no || null,
            hourly_rate,
            hire_date,
            is_active: is_active === "true"
        });

        res.redirect("/workers");

    } catch (error) {
        console.error(error);

        res.render("workers/add", {
            user: req.user,
            error: "Unable to add worker. Please check your information."
        });
    }
};


exports.showEditForm = async (req, res) => {
    try {
        const worker = await Worker.findByPk(req.params.id);

        if (!worker) {
            return res.status(404).send("Worker not found.");
        }

        res.render("workers/edit", {
            user: req.user,
            worker,
            error: null
        });

    } catch (error) {
        console.error(error);
        res.status(500).send("Error loading worker.");
    }
};


exports.update = async (req, res) => {
    try {
        const worker = await Worker.findByPk(req.params.id);

        if (!worker) {
            return res.status(404).send("Worker not found.");
        }

        const {
            first_name,
            last_name,
            contact_no,
            hourly_rate,
            hire_date,
            is_active
        } = req.body;

        await worker.update({
            first_name,
            last_name,
            contact_no: contact_no || null,
            hourly_rate,
            hire_date,
            is_active: is_active === "true"
        });

        res.redirect("/workers");

    } catch (error) {
        console.error(error);
        res.status(500).send("Error updating worker.");
    }
};


exports.delete = async (req, res) => {
    try {
        await Worker.destroy({
            where: {
                worker_id: req.params.id
            }
        });

        res.redirect("/workers");

    } catch (error) {
        console.error(error);
        res.status(500).send("Error deleting worker.");
    }
};