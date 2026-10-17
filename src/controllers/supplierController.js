const {
    Supplier
} = require("../models");


exports.index = async (req, res) => {
    try {

        const suppliers = await Supplier.findAll({
            order: [
                ["supplier_id", "DESC"]
            ]
        });

        res.render("suppliers/index", {
            user: req.user,
            suppliers
        });

    } catch (error) {

        console.error("SUPPLIER LIST ERROR:");
        console.error(error);

        res.status(500).send(
            "Error loading suppliers."
        );

    }
};


exports.showAddForm = (req, res) => {

    res.render("suppliers/add", {
        user: req.user,
        error: null
    });

};


exports.create = async (req, res) => {
    try {

        const {
            name,
            contact_no,
            address
        } = req.body;


        await Supplier.create({

            name: name,

            contact_no:
                contact_no || null,

            address:
                address || null

        });


        res.redirect(
            "/suppliers"
        );


    } catch (error) {

        console.error("SUPPLIER CREATE ERROR:");
        console.error(error);


        res.render("suppliers/add", {
            user: req.user,
            error: error.message
        });

    }
};


exports.showEditForm = async (req, res) => {
    try {

        const supplier =
            await Supplier.findByPk(
                req.params.id
            );


        if (!supplier) {

            return res.status(404).send(
                "Supplier not found."
            );

        }


        res.render("suppliers/edit", {
            user: req.user,
            supplier,
            error: null
        });


    } catch (error) {

        console.error("SUPPLIER EDIT FORM ERROR:");
        console.error(error);

        res.status(500).send(
            "Error loading supplier."
        );

    }
};


exports.update = async (req, res) => {
    try {

        const supplier =
            await Supplier.findByPk(
                req.params.id
            );


        if (!supplier) {

            return res.status(404).send(
                "Supplier not found."
            );

        }


        const {
            name,
            contact_no,
            address
        } = req.body;


        await supplier.update({

            name: name,

            contact_no:
                contact_no || null,

            address:
                address || null

        });


        res.redirect(
            "/suppliers"
        );


    } catch (error) {

        console.error("SUPPLIER UPDATE ERROR:");
        console.error(error);

        res.status(500).send(
            "Error updating supplier."
        );

    }
};


exports.delete = async (req, res) => {
    try {

        await Supplier.destroy({
            where: {
                supplier_id:
                    req.params.id
            }
        });


        res.redirect(
            "/suppliers"
        );


    } catch (error) {

        console.error("SUPPLIER DELETE ERROR:");
        console.error(error);

        res.status(500).send(
            "Error deleting supplier."
        );

    }
};