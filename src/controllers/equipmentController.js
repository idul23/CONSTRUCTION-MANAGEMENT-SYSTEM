const { Equipment } = require("../models");

exports.index = async (req, res) => {
    try {
        const equipment = await Equipment.findAll({
            order: [["equipment_id", "DESC"]]
        });

        res.render("equipment/index", {
            user: req.user,
            equipment
        });
    } catch (error) {
        console.error("EQUIPMENT LIST ERROR:");
        console.error(error);
        res.status(500).send("Error loading equipment.");
    }
};

exports.showAddForm = (req, res) => {
    res.render("equipment/add", {
        user: req.user,
        error: null
    });
};

exports.create = async (req, res) => {
    try {
        const {
            name,
            equip_type,
            serial_no,
            daily_rate,
            condition
        } = req.body;

        await Equipment.create({
            name,
            equip_type,
            serial_no,
            daily_rate: Number(daily_rate || 0),
            condition
        });

        res.redirect("/equipment");
    } catch (error) {
        console.error("EQUIPMENT CREATE ERROR:");
        console.error(error);

        res.render("equipment/add", {
            user: req.user,
            error: error.message
        });
    }
};

exports.showEditForm = async (req, res) => {
    try {
        const equipment = await Equipment.findByPk(req.params.id);

        if (!equipment) {
            return res.status(404).send("Equipment not found.");
        }

        res.render("equipment/edit", {
            user: req.user,
            equipment,
            error: null
        });
    } catch (error) {
        console.error("EQUIPMENT EDIT FORM ERROR:");
        console.error(error);
        res.status(500).send("Error loading equipment.");
    }
};

exports.update = async (req, res) => {
    try {
        const equipment = await Equipment.findByPk(req.params.id);

        if (!equipment) {
            return res.status(404).send("Equipment not found.");
        }

        const {
            name,
            equip_type,
            serial_no,
            daily_rate,
            condition
        } = req.body;

        await equipment.update({
            name,
            equip_type,
            serial_no,
            daily_rate: Number(daily_rate || 0),
            condition
        });

        res.redirect("/equipment");
    } catch (error) {
        console.error("EQUIPMENT UPDATE ERROR:");
        console.error(error);
        res.status(500).send("Error updating equipment.");
    }
};

exports.delete = async (req, res) => {
    try {
        await Equipment.destroy({
            where: {
                equipment_id: req.params.id
            }
        });

        res.redirect("/equipment");
    } catch (error) {
        console.error("EQUIPMENT DELETE ERROR:");
        console.error(error);
        res.status(500).send("Error deleting equipment.");
    }
};