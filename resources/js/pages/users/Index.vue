<template>
    <div>
        <div class="flex items-center justify-between">
            <div
                class="self-stretch justify-start text-[#002d45] text-[32px] font-semibold capitalize mb-[48px]"
            >
                {{ $t("Users") }}
            </div>
        </div>

        <BaseTable
            routeSync
            :loading="loading"
            :columns="columns"
            :total="total"
            v-model:page="page"
            v-model:pageSize="pageSize"
            v-model:search="search"
            :enableSearch="true"
            searchPlaceholder="Search here"
            :enableSort="true"
            :stickyHeader="true"
            @request="fetchData"
        >
            <template #default="{ rowStart }">
                <TableRows
                    :users="users"
                    :columns="columns"
                    :rowStart="rowStart"
                />
            </template>
        </BaseTable>
    </div>
</template>

<script setup>
import { ref, watch } from "vue";
import axios from "axios";
import BaseTable from "@/components/table/BaseTable.vue";
import TableRows from "@/pages/users/components/TableRows.vue";
import { useRoute } from "vue-router";

const route = useRoute();
const users = ref([]);
const loading = ref(false);
const error = ref("");

const columns = [
    { key: "full_name", label: "Full Name", sortable: true, width: "200px" },
    { key: "email", label: "Email Address", sortable: true, width: "260px" },
    { key: "status", label: "Profile Status", sortable: true, width: "140px" },
];

const page = ref(1);
const pageSize = ref(10);
const search = ref("");
const total = ref(0);

// Filters initial state
const filters = ref({
    status: "",
    role: "",
});

function firstSortableKey() {
    const col = columns.find(
        (c) => typeof c === "string" || c.sortable !== false
    );
    if (!col) return "id";
    return typeof col === "string" ? col : col.key;
}

async function fetchData({ page: p, pageSize: ps, sort, search: q }) {
    loading.value = true;
    error.value = "";
    try {
        const sortBy = sort?.by || firstSortableKey(); // 👈 fallback to first column
        const sortDir = sort?.dir || "desc"; // 👈 default to desc

        const res = await axios.get("/users", {
            params: {
                page: p,
                per_page: ps,
                sort_by: sortBy,
                sort_dir: sortDir,
                search: q || "",
                filters: filters.value,
            },
        });

        users.value = Array.isArray(res.data?.data) ? res.data.data : [];

        const meta = res.data?.response?.meta ?? res.data?.meta ?? {};
        total.value = meta.total ?? 0;
        page.value = meta.current_page ?? p;
        pageSize.value = meta.per_page ?? ps;
    } catch (e) {
        error.value = "Failed to fetch users.";
    } finally {
        loading.value = false;
    }
}
</script>
