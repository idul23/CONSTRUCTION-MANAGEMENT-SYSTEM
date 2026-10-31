const express = require("express");
const path = require("path");
const cookieParser = require("cookie-parser");
const helmet = require("helmet");

require("dotenv").config();

const sequelize = require("./config/database");
const { User, Role } = require("./models");

const authRoutes = require("./routes/authRoutes");
const dashboardRoutes = require("./routes/dashboardRoutes");
const projectRoutes = require("./routes/projectRoutes");
const clientRoutes = require("./routes/clientRoutes");
const workerRoutes = require("./routes/workerRoutes");
const taskRoutes = require("./routes/taskRoutes");
const phaseRoutes = require("./routes/phaseRoutes");
const attendanceRoutes = require("./routes/attendanceRoutes");
const assignmentRoutes = require("./routes/assignmentRoutes");
const materialRoutes = require("./routes/materialRoutes");
const supplierRoutes = require("./routes/supplierRoutes");
const materialUsageRoutes = require("./routes/materialUsageRoutes");
const equipmentRoutes = require("./routes/equipmentRoutes");
const equipmentUsageRoutes = require("./routes/equipmentUsageRoutes");
const expenseRoutes = require("./routes/expenseRoutes");
const reportRoutes = require("./routes/reportRoutes");
const auditRoutes = require("./routes/auditRoutes");
const userRoutes = require("./routes/userRoutes");
const workerSkillRoutes = require("./routes/workerSkillRoutes");
const projectMemberRoutes = require("./routes/projectMemberRoutes");
const userProfileRoutes = require("./routes/userProfileRoutes");

const app = express();

app.disable("x-powered-by");

app.set("view engine", "ejs");
app.set("views", path.join(__dirname, "views"));

app.use(
    helmet({
        contentSecurityPolicy: false
    })
);

app.use(express.urlencoded({
    extended: true,
    limit: "100kb"
}));

app.use(express.json({
    limit: "100kb"
}));

app.use(cookieParser());

app.use(express.static(path.join(__dirname, "..", "public")));

// APPLICATION ROUTES
app.use("/", authRoutes);
app.use("/", dashboardRoutes);
app.use("/", projectRoutes);
app.use("/", clientRoutes);
app.use("/", workerRoutes);
app.use("/", taskRoutes);
app.use("/", phaseRoutes);
app.use("/", attendanceRoutes);
app.use("/", assignmentRoutes);
app.use("/", materialRoutes);
app.use("/", supplierRoutes);
app.use("/", materialUsageRoutes);
app.use("/", equipmentRoutes);
app.use("/", equipmentUsageRoutes);
app.use("/", expenseRoutes);
app.use("/", reportRoutes);
app.use("/", auditRoutes);
app.use("/", userRoutes);
app.use("/", workerSkillRoutes);
app.use("/", projectMemberRoutes);
app.use("/", userProfileRoutes);

app.get("/", (req, res) => {
    res.redirect("/login");
});

// ERROR HANDLING
app.use((err, req, res, next) => {
    if (res.headersSent) {
        return next(err);
    }

    if (err.type === "entity.too.large") {
        return res.status(413).send("Request is too large.");
    }

    if (err.type === "entity.parse.failed") {
        return res.status(400).send("Invalid request data.");
    }

    console.error("Request error:", err.message);

    res.status(500).send("An unexpected error occurred.");
});

const PORT = process.env.PORT || 3000;

async function startServer() {
    try {
        await sequelize.authenticate();

        const [dbInfo] = await sequelize.query(`
    SELECT
        current_database() AS database_name,
        current_schema() AS schema_name,
        current_setting('search_path') AS search_path
`);

console.log("NODE DATABASE INFO:", dbInfo);

const [constraints] = await sequelize.query(`
    SELECT
        conname,
        pg_get_constraintdef(oid) AS definition
    FROM pg_constraint
    WHERE conrelid = 'public.projects'::regclass
`);

console.log("PROJECT CONSTRAINTS:", constraints);

        console.log("PostgreSQL connected successfully.");

        await sequelize.sync();

        console.log("Database tables created successfully.");

        const [adminRole] = await Role.findOrCreate({
            where: { role_name: "Administrator" }
        });

        await Role.findOrCreate({
            where: { role_name: "Project Manager" }
        });

        await Role.findOrCreate({
            where: { role_name: "Site Staff" }
        });

        await User.findOrCreate({
            where: { username: "admin" },
            defaults: {
                role_id: adminRole.role_id,
                username: "admin",
                email: "admin@example.com",
                password_hash: "$2b$12$omTfbgF9tpEhXg77nkfLq.uNqTsuhMVR6L7TNx0894gbaBB.PGSIa",
                is_active: true
            }
        });

        console.log("Initial roles and Administrator account checked.");

        app.listen(PORT, () => {
            console.log(`Server running on port ${PORT}`);
        });

    } catch (error) {
        console.error(
            "Database connection or synchronization failed:",
            error
        );

        process.exitCode = 1;
    }
}

startServer();