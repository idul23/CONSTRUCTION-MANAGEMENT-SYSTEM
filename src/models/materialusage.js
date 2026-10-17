const { DataTypes } = require("sequelize");
const sequelize = require("../config/database");

const MaterialUsage = sequelize.define(
    "MaterialUsage",
    {
        usage_id: {
            type: DataTypes.INTEGER,
            primaryKey: true,
            autoIncrement: true
        },

        task_id: {
            type: DataTypes.INTEGER,
            allowNull: false
        },

        material_id: {
            type: DataTypes.INTEGER,
            allowNull: false
        },

        recorded_by: {
            type: DataTypes.INTEGER,
            allowNull: false
        },

        quantity_used: {
            type: DataTypes.DECIMAL(12, 2),
            allowNull: false
        },

        cost_snapshot: {
            type: DataTypes.DECIMAL(12, 2),
            allowNull: false
        },

        usage_date: {
            type: DataTypes.DATEONLY,
            allowNull: false
        }
    },
    {
        tableName: "material_usage",
        timestamps: false
    }
);

module.exports = MaterialUsage;