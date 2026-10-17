const {
    MaterialUsage,
    Task,
    Material
} = require("../models");

exports.index = async (req, res) => {
    try {
        const usageRecords = await MaterialUsage.findAll({
            order: [["usage_id", "DESC"]]
        });

        const usage = [];

        for (const record of usageRecords) {
            const task = await Task.findByPk(record.task_id);
            const material = await Material.findByPk(record.material_id);

            usage.push({
                usage_id: record.usage_id,
                task: task,
                material: material,
                quantity_used: record.quantity_used,
                cost_snapshot: record.cost_snapshot,
                usage_date: record.usage_date
            });
        }

        res.render("material-usage/index", {
            user: req.user,
            usage
        });
    } catch (error) {
        console.error("MATERIAL USAGE LIST ERROR:");
        console.error(error);
        res.status(500).send("Error loading material usage.");
    }
};

exports.showAddForm = async (req, res) => {
    try {
        const tasks = await Task.findAll({
            order: [["task_id", "DESC"]]
        });

        const materials = await Material.findAll({
            order: [["name", "ASC"]]
        });

        res.render("material-usage/add", {
            user: req.user,
            tasks,
            materials,
            error: null
        });
    } catch (error) {
        console.error("MATERIAL USAGE ADD FORM ERROR:");
        console.error(error);
        res.status(500).send("Error loading material usage form.");
    }
};

exports.create = async (req, res) => {
    try {
        const {
            task_id,
            material_id,
            quantity_used,
            usage_date
        } = req.body;

        const material = await Material.findByPk(material_id);

        if (!material) {
            return res.status(404).send("Material not found.");
        }

        const quantity = Number(quantity_used || 0);
        const costSnapshot = Number(material.unit_cost || 0);

        await MaterialUsage.create({
            task_id: Number(task_id),
            material_id: Number(material_id),
            recorded_by: req.user.user_id,
            quantity_used: quantity,
            cost_snapshot: costSnapshot,
            usage_date: usage_date
        });

        res.redirect("/material-usage");
    } catch (error) {
        console.error("MATERIAL USAGE CREATE ERROR:");
        console.error(error);
        res.status(500).send(
            "Error creating material usage: " + error.message
        );
    }
};

exports.showEditForm = async (req, res) => {
    try {
        const usageRecord = await MaterialUsage.findByPk(req.params.id);

        if (!usageRecord) {
            return res.status(404).send(
                "Material usage record not found."
            );
        }

        const tasks = await Task.findAll({
            order: [["task_id", "DESC"]]
        });

        const materials = await Material.findAll({
            order: [["name", "ASC"]]
        });

        res.render("material-usage/edit", {
            user: req.user,
            usageRecord,
            tasks,
            materials,
            error: null
        });
    } catch (error) {
        console.error("MATERIAL USAGE EDIT FORM ERROR:");
        console.error(error);
        res.status(500).send("Error loading material usage.");
    }
};

exports.update = async (req, res) => {
    try {
        const usageRecord = await MaterialUsage.findByPk(req.params.id);

        if (!usageRecord) {
            return res.status(404).send(
                "Material usage record not found."
            );
        }

        const {
            task_id,
            material_id,
            quantity_used,
            usage_date
        } = req.body;

        const material = await Material.findByPk(material_id);

        if (!material) {
            return res.status(404).send("Material not found.");
        }

        const quantity = Number(quantity_used || 0);
        const costSnapshot = Number(material.unit_cost || 0);

        await usageRecord.update({
            task_id: Number(task_id),
            material_id: Number(material_id),
            quantity_used: quantity,
            cost_snapshot: costSnapshot,
            usage_date: usage_date
        });

        res.redirect("/material-usage");
    } catch (error) {
        console.error("MATERIAL USAGE UPDATE ERROR:");
        console.error(error);
        res.status(500).send(
            "Error updating material usage: " + error.message
        );
    }
};

exports.delete = async (req, res) => {
    try {
        await MaterialUsage.destroy({
            where: {
                usage_id: req.params.id
            }
        });

        res.redirect("/material-usage");
    } catch (error) {
        console.error("MATERIAL USAGE DELETE ERROR:");
        console.error(error);
        res.status(500).send("Error deleting material usage.");
    }
};