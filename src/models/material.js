const { DataTypes } = require("sequelize");
const sequelize = require("../config/database");

const Material = sequelize.define(
    "Material",
    {
        material_id: {
            type: DataTypes.INTEGER,
            primaryKey: true,
            autoIncrement: true
        },

        supplier_id: {
            type: DataTypes.INTEGER,
            allowNull: false
        },

        name: {
            type: DataTypes.STRING(120),
            allowNull: false
        },

        category: {
            type: DataTypes.STRING(80),
            allowNull: true
        },

        unit: {
            type: DataTypes.STRING(30),
            allowNull: false
        },

        unit_cost: {
            type: DataTypes.DECIMAL(12, 2),
            allowNull: false
        },

        stock_qty: {
            type: DataTypes.DECIMAL(12, 2),
            allowNull: false,
            defaultValue: 0
        },

        reorder_level: {
            type: DataTypes.DECIMAL(12, 2),
            allowNull: false,
            defaultValue: 0
        }
    },
    {
        tableName: "materials",
        timestamps: false
    }
);

module.exports = Material;