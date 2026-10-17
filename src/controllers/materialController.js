const {
    Material,
    Supplier
} = require("../models");


exports.index = async (req, res) => {
    try {

        const materials = await Material.findAll({
            include: [
                {
                    model: Supplier
                }
            ],
            order: [
                ["material_id", "DESC"]
            ]
        });

        res.render("materials/index", {
            user: req.user,
            materials
        });

    } catch (error) {

        console.error("MATERIAL LIST ERROR:");
        console.error(error);

        res.status(500).send(
            "Error loading materials."
        );

    }
};


exports.showAddForm = async (req, res) => {
    try {

        const suppliers = await Supplier.findAll({
            order: [
                ["name", "ASC"]
            ]
        });

        res.render("materials/add", {
            user: req.user,
            suppliers,
            error: null
        });

    } catch (error) {

        console.error("MATERIAL ADD FORM ERROR:");
        console.error(error);

        res.status(500).send(
            "Error loading add material page."
        );

    }
};


exports.create = async (req, res) => {
    try {

        const {
            supplier_id,
            name,
            category,
            unit,
            unit_cost,
            stock_qty,
            reorder_level
        } = req.body;

        await Material.create({

            supplier_id: Number(supplier_id),

            name: name,

            category: category,

            unit: unit,

            unit_cost: Number(unit_cost || 0),

            stock_qty: Number(stock_qty || 0),

            reorder_level: Number(reorder_level || 0)

        });

        res.redirect("/materials");

    } catch (error) {

        console.error("MATERIAL CREATE ERROR:");
        console.error(error);

        try {

            const suppliers = await Supplier.findAll({
                order: [
                    ["name", "ASC"]
                ]
            });

            res.render("materials/add", {
                user: req.user,
                suppliers,
                error: error.message
            });

        } catch (secondError) {

            console.error(secondError);

            res.status(500).send(
                "Error creating material."
            );

        }
    }
};


exports.showEditForm = async (req, res) => {
    try {

        const material = await Material.findByPk(
            req.params.id
        );

        if (!material) {
            return res.status(404).send(
                "Material not found."
            );
        }

        const suppliers = await Supplier.findAll({
            order: [
                ["name", "ASC"]
            ]
        });

        res.render("materials/edit", {
            user: req.user,
            material,
            suppliers,
            error: null
        });

    } catch (error) {

        console.error("MATERIAL EDIT FORM ERROR:");
        console.error(error);

        res.status(500).send(
            "Error loading material."
        );

    }
};


exports.update = async (req, res) => {
    try {

        const material = await Material.findByPk(
            req.params.id
        );

        if (!material) {
            return res.status(404).send(
                "Material not found."
            );
        }

        const {
            supplier_id,
            name,
            category,
            unit,
            unit_cost,
            stock_qty,
            reorder_level
        } = req.body;

        await material.update({

            supplier_id: Number(supplier_id),

            name: name,

            category: category,

            unit: unit,

            unit_cost: Number(unit_cost || 0),

            stock_qty: Number(stock_qty || 0),

            reorder_level: Number(reorder_level || 0)

        });

        res.redirect("/materials");

    } catch (error) {

        console.error("MATERIAL UPDATE ERROR:");
        console.error(error);

        res.status(500).send(
            "Error updating material."
        );

    }
};


exports.delete = async (req, res) => {
    try {

        await Material.destroy({
            where: {
                material_id: req.params.id
            }
        });

        res.redirect("/materials");

    } catch (error) {

        console.error("MATERIAL DELETE ERROR:");
        console.error(error);

        res.status(500).send(
            "Error deleting material."
        );

    }
};