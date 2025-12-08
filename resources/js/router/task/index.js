const AuthenticatedLayout = () => import("@/layouts/AuthenticatedLayout.vue");

export default [
    {
        path: "/cms/Task",
        component: AuthenticatedLayout,
        children: [
            {
                path: "",
                name: "TaskIndex",
                component: () => import("@/pages/Tasks/Index.vue"),
                meta: {
                    requiresAuth: true,
                    title: "Task Management",
                },
            },
            {
                path: "create",
                name: "TaskCreate",
                component: () =>
                    import("@/pages/Tasks/CreateOrUpdate.vue"),
                meta: { requiresAuth: true, title: "Create Task" },
            },
            {
                path: "update/:id",
                name: "TaskUpdate",
                component: () =>
                    import("@/pages/Tasks/CreateOrUpdate.vue"),
                meta: { requiresAuth: true, title: "Update Task" },
            },
            {
                path: ":id",
                name: "TaskDetails",
                component: () => import("@/pages/Tasks/Details.vue"),
                meta: {
                    requiresAuth: true,
                    title: "Task Details",
                },
            },
        ],
    },
];
