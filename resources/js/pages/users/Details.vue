<template>
    <div>
        <GoBack class="mt-2 mb-[22px]" />

        <div v-if="loading" class="text-center py-10">Loading...</div>
        <div v-else-if="error" class="text-center py-10 text-red-500">
            {{ error }}
        </div>

        <div v-else>
            <div class="flex items-center justify-between">
                <div
                    class="self-stretch justify-start text-[#002d45] text-[32px] font-semibold capitalize mb-[22px]"
                >
                    {{ $t("User Details") }}
                </div>
            </div>

            <div
                class="self-stretch p-8 bg-white rounded-2xl inline-flex flex-col items-start w-full h-[calc(100vh-250px)]"
            >
                <div class="w-full h-[calc(100vh-250px)] overflow-y-auto">
                    <div class="w-full flex flex-col gap-8">
                        <!-- Personal Info -->
                        <div>
                            <FormHeader :label="'Personal Info'" />
                            <div
                                class="form-row-grid mt-[28px]"
                            >
                                <FormValue label="ID" :value="user?.uuid" :translate="true" />
                                <FormValue label="Full Name" :value="user?.full_name" :translate="true" />
                                <FormValue label="Email" :value="user?.email" />
                                <FormValue label="Gender" :value="user?.gender" :translate="true" />
                                <FormValue label="Date of Birth" :value="user?.date_of_birth ? new Date(user.date_of_birth).toLocaleDateString('de-DE') : '--'" />
                            </div>
                        </div>

                        <!-- Session Summary -->
                        <div>
                            <FormHeader :label="'Session Summary'" />
                            <FormValue label="Session Counts" :value="user?.session_count" />
                        </div>

                        <!-- Feedback Summary -->
                        <div>
                            <FormHeader :label="'Feedback Summary'" />
                            <FormValue label="Feedback Counts" :value="user?.feedback_count" />
                        </div>

                        <!-- My Voice -->
                        <div v-if="user.cloned_voice_url">
                            <FormHeader :label="'My Voice'" />
                            <div
                                class="w-full grid grid-cols-2 gap-4 mt-[28px]"
                            >
                                <div>
                                    <MusicPreview :src="user.cloned_voice_url" :audioName="user.voice_title" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import GoBack from "@/components/common/GoBack.vue";
import MusicPreview from "@/components/form/MusicPreview.vue";
import { ref, onMounted } from "vue";
import axios from "axios";
import { useRoute } from "vue-router";
import FormValue from "@/components/form/FormValue.vue";
import FormHeader from "@/components/form/FormHeader.vue";

const vRoute = useRoute();

// --- Reactive state ---
const user = ref(null);
const userID = vRoute.params.id;
const loading = ref(true);
const error = ref(null);

// Fetch user details from API
const fetchUserDetails = async () => {
    try {
        const { data } = await axios.get("/users/" + userID);
        user.value = data.data;
    } catch (err) {
        error.value = err.message || "Failed to load user details";
    } finally {
        loading.value = false;
    }
};

// Utility: shorten filename
const shortFileName = (file) => {
    const name = file?.name || "Unknown";
    return name.length > 15 ? name.slice(0, 12) + "..." : name;
};

// Auto-fetch on page load
onMounted(fetchUserDetails);
</script>
