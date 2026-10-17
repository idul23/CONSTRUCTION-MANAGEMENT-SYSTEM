const { DataTypes } = require("sequelize");
const sequelize = require("../config/database");

const Supplier = sequelize.define(
    "Supplier",
    {
        supplier_id: {
            type: DataTypes.INTEGER,
            primaryKey: true,
            autoIncrement: true
        },

        name: {
            type: DataTypes.STRING(120),
            allowNull: false
        },

        contact_no: {
            type: DataTypes.STRING(30),
            allowNull: true
        },

        address: {
            type: DataTypes.STRING(200),
            allowNull: true
        }
    },
    {
        tableName: "suppliers",
        timestamps: false
    }
);

module.exports = Supplier;