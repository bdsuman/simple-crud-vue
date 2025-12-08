const AuthenticatedLayout = () => import("@/layouts/AuthenticatedLayout.vue");

export default [
    {
        path: "/cms/testimonial",
        component: AuthenticatedLayout,
        children: [
            {
                path: "",
                name: "TestimonialIndex",
                component: () => import("@/pages/testimonial/Index.vue"),
                meta: {
                    requiresAuth: true,
                    title: "Testimonial Management",
                },
            },
            {
                path: "create",
                name: "TestimonialCreate",
                component: () =>
                    import("@/pages/testimonial/CreateOrUpdate.vue"),
                meta: { requiresAuth: true, title: "Create Testimonial" },
            },
            {
                path: "update/:id",
                name: "TestimonialUpdate",
                component: () =>
                    import("@/pages/testimonial/CreateOrUpdate.vue"),
                meta: { requiresAuth: true, title: "Update Testimonial" },
            },
            {
                path: ":id",
                name: "TestimonialDetails",
                component: () => import("@/pages/testimonial/Details.vue"),
                meta: {
                    requiresAuth: true,
                    title: "Testimonial Details",
                },
            },
        ],
    },
];
