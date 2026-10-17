const { DataTypes } = require("sequelize");
const sequelize = require("../config/database");

const Worker = sequelize.define(
    "Worker",
    {
        worker_id: {
            type: DataTypes.INTEGER,
            primaryKey: true,
            autoIncrement: true
        },

        first_name: {
            type: DataTypes.STRING(80),
            allowNull: false
        },

        last_name: {
            type: DataTypes.STRING(80),
            allowNull: false
        },

        contact_no: {
            type: DataTypes.STRING(30),
            allowNull: true
        },

        hourly_rate: {
            type: DataTypes.DECIMAL(10, 2),
            allowNull: false
        },

        hire_date: {
            type: DataTypes.DATEONLY,
            allowNull: false
        },

        is_active: {
            type: DataTypes.BOOLEAN,
            allowNull: false,
            defaultValue: true
        }
    },
    {
        tableName: "workers",
        timestamps: false
    }
);

module.exports = Worker;