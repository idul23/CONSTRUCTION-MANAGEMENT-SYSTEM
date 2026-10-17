const { DataTypes } = require("sequelize");
const sequelize = require("../config/database");

const EquipmentUsage = sequelize.define(
    "EquipmentUsage",
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

        equipment_id: {
            type: DataTypes.INTEGER,
            allowNull: false
        },

        recorded_by: {
            type: DataTypes.INTEGER,
            allowNull: false
        },

        date_used: {
            type: DataTypes.DATEONLY,
            allowNull: false
        },

        hours_used: {
            type: DataTypes.DECIMAL(8, 2),
            allowNull: false
        },

        usage_cost: {
            type: DataTypes.DECIMAL(12, 2),
            allowNull: false
        }
    },
    {
        tableName: "equipment_usage",
        timestamps: false
    }
);

module.exports = EquipmentUsage;