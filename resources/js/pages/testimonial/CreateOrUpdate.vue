<template>
    <div>
        <GoBack class="mt-2 mb-[22px]" />
        <div class="flex items-center justify-between">
            <div
                class="self-stretch justify-start text-[#002d45] text-[32px] font-semibold mb-[22px]"
            >
                {{ $t(isEdit ? "edit" : "add") }}
                {{ $t("success_story") }}
            </div>
            <LanguageDropdown
                v-if="isEdit"
                v-model="form.language"
                @changed="(v) => getDetails(v)"
            />
        </div>
        <div
            class="self-stretch p-8 bg-white rounded-2xl inline-flex flex-col items-end w-full !min-h-[calc(100vh-250px)] overflow-y-auto"
        >
            <div class="w-full grid grid-cols-2 gap-4">
                <div
                    class="w-full inline-flex flex-col justify-start items-start gap-3"
                >
                    <TextInput
                        label="title"
                        :placeholder="$t('enter_title')"
                        required
                        :maxLength="100"
                        v-model="form.title"
                    />
                </div>
                <div
                    class="w-full inline-flex flex-col justify-start items-start gap-3"
                >
                    <TextInput
                        label="name"
                        :placeholder="$t('enter_name')"
                        required
                        :maxLength="100"
                        v-model="form.author_name"
                    />
                </div>
                <div
                    class="w-full inline-flex flex-col justify-start items-start gap-3"
                >
                    <TextInput
                        label="profession"
                        :placeholder="$t('enter_profession')"
                        required
                        :maxLength="100"
                        v-model="form.job_title"
                    />
                </div>
            </div>

            <div class="w-full grid grid-cols-2 gap-4 mt-4">
                <div class="flex-col justify-start items-start flex">
                    <DragAndDropUpload
                        v-model="form.avatar"
                        required
                        class="w-full"
                        label="image"
                        attachmentLabelClass="!py-10"
                        :placeholder="
                            $t('click_or_drag_&_drop_files_here_to_upload')
                        "
                        :show-file-names="true"
                        :maxFiles="1"
                        :maxFileSize="20"
                        :allowedExtensions="['.jpg', '.JPEG', '.png']"
                        :extraClass="v$.avatar.$error ? '!border-red-500' : ''"
                        @blur="validate('avatar')"
                        :modelValue="form.avatar"
                    />
                    <div
                        v-if="v$.avatar.$errors"
                        class="justify-start text-xs font-normal leading-none text-red-500"
                    >
                        {{ v$.avatar.$errors?.[0]?.$message }}
                    </div>
                </div>
            </div>
            <div
                class="w-full inline-flex flex-col justify-start items-start gap-3 mt-4"
            >
                <TextInput
                    label="text"
                    :placeholder="$t('write')"
                    required
                    :maxLength="200"
                    v-model="form.content"
                    :isTextarea="true"
                    :helpText="$t('max_200_characters')"
                />
            </div>
            <div class="flex gap-4 mt-[40px]">
                <OutlineButton :show="true" title="reset" @click="resetForm" />
                <Button
                    :title="isEdit ? 'update' : 'add'"
                    :width="'w-40'"
                    @click="handleSubmit"
                />
            </div>
        </div>
    </div>
</template>
<script setup>
import axios from "axios";
import GoBack from "@/components/common/GoBack.vue";
import TextInput from "@/components/form/TextInput.vue";
import Button from "@/components/buttons/Button.vue";
import OutlineButton from "@/components/buttons/OutlineButton.vue";
import DragAndDropUpload from "@/components/form/DragAndDropSingleUpload.vue";
import Form from "vform";
import { useNotify } from "@/composables/useNotification";
import { computed, onMounted, reactive } from "vue";
import { useVuelidate } from "@vuelidate/core";
import { required, helpers } from "@vuelidate/validators";
import { trans } from "laravel-vue-i18n";
import { useRoute, useRouter } from "vue-router";
import LanguageDropdown from "@/components/form/LanguageDropdown.vue";
import { useUserStore } from "@/stores/useUserStore";
const store = useUserStore();
const notify = useNotify();
const vRoute = useRoute();
const vRouter = useRouter();

const props = defineProps(["show", "editItemId"]);
const isEdit = vRoute.params?.id || null;

const form = reactive(
    new Form({
        author_name: "",
        job_title: "",
        title: "",
        content: "",
        language: store.user?.language ?? "de",
        avatar: null,
        _method: isEdit ? "put" : "post",
    })
);

const validate = (fieldName) => {
    v$.value[fieldName].$touch();
};

const dynamicRules = computed(() => ({
    avatar: {
        required: helpers.withMessage(
            trans("field_input_is_missing"),
            required
        ),
    },
}));

const v$ = useVuelidate(dynamicRules, form);

const handleCancel = () => {
    isEdit ? vRouter.push({ name: "TestimonialIndex" }) : resetForm();
};
const resetForm = () => {
    form.reset();
    form.clear();
    v$.value.$reset();
};
const handleSubmit = async () => {
    v$.value.$touch();
    if (v$.value.$invalid) {
        return;
    }
    try {
        form.avatar = isEdit ? resetIfUrlObject(form.avatar) : form.avatar;

        await axios.post(
            isEdit
                ? route("testimonials.update", {
                      testimonial: isEdit,
                  })
                : route("testimonials.store"),
            form,
            { headers: { "Content-Type": "multipart/form-data" } }
        );
        let message = isEdit
            ? "success_story_updated"
            : "success_story_created";
        notify.success({
            message: trans(message),
        });

        vRouter.push({ name: "TestimonialIndex" });
    } catch (error) {
        // console.error(error);
    }
};
const resetIfUrlObject = (field) => {
    if (typeof field === "object" && /^https?:\/\//.test(field?.url)) {
        return null;
    }
    return field;
};
// Get Data for edit
const getDetails = async (lang) => {
    try {
        const { data } = await axios.get(
            route("testimonials.show", { testimonial: isEdit }),
            {
                headers: {
                    "X-Request-Language": lang,
                },
            }
        );
        form.author_name = data.data.author_name;
        form.job_title = data.data.job_title;
        form.title = data.data.title;
        form.content = data.data.content;
        form.avatar = data?.data?.avatar_url
            ? { url: data.data.avatar_url, file_type: "image" }
            : null;
    } catch (error) {
        // console.error("error_fetching_data_for_edit:", error);
    }
};

onMounted(async () => {
    if (isEdit) {
        await getDetails(form.language);
    }
});
</script>
