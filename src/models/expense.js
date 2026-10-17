const { DataTypes } = require("sequelize");
const sequelize = require("../config/database");

const Expense = sequelize.define(
    "Expense",
    {
        expense_id: {
            type: DataTypes.INTEGER,
            primaryKey: true,
            autoIncrement: true
        },

        project_id: {
            type: DataTypes.INTEGER,
            allowNull: false
        },

        recorded_by: {
            type: DataTypes.INTEGER,
            allowNull: false
        },

        category: {
            type: DataTypes.STRING(30),
            allowNull: false
        },

        amount: {
            type: DataTypes.DECIMAL(14, 2),
            allowNull: false
        },

        description: {
            type: DataTypes.STRING(250),
            allowNull: true
        },

        expense_date: {
            type: DataTypes.DATEONLY,
            allowNull: false
        }
    },
    {
        tableName: "expenses",
        timestamps: false
    }
);

module.exports = Expense;