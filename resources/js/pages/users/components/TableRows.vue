<template>
    <tbody>
        <tr
            v-for="(row, idx) in users"
            :key="row.id ?? idx"
        >
            <!-- Name -->
            <td class="text-[#3F8B77] underline" :style="{ width: getWidth('full_name') }" >
                <span class="cursor-pointer" @click="handleDetails(row.id)">{{ row.full_name }}</span>
            </td>

            <!-- Email -->
            <td :style="{ width: getWidth('email') }">
                {{ row.email }}
            </td>
            
            <!-- Status -->
            <td :style="{ width: getWidth('status') }">
                <Badge :label="row.status" :type="row.status" />
            </td>
        </tr>
    </tbody>
</template>

<script setup>
import { ref, shallowRef } from "vue";
import ThreeDotsVerticalIcon from "@/components/icons/ThreeDotsVerticalIcon.vue";
import ActionPanel from "@/components/form/ActionPanel.vue";
import { useRoute, useRouter } from "vue-router";
import Badge from "@/components/common/Badge.vue";

const props = defineProps({
    users: { type: Array, default: () => [] },
    columns: { type: Array, required: true },
    rowStart: { type: Number, default: 1 },
});

const emit = defineEmits(["refetch", "action"]);

function getWidth(key) {
    const col = props.columns.find((c) => c.key === key);
    return col?.width || "auto";
}

const route = useRoute();
const router = useRouter();

const handleDetails = (id) => {
    router.push({ name: "UserDetails", params: { id } });
};
</script>
