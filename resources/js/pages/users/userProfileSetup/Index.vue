<template>
  <div>
    <!-- Heading OUTSIDE -->
    <div class="flex items-center justify-between">
        <div
            class="self-stretch justify-start text-[#002d45] text-[32px] font-semibold mb-[22px]"
        >
            {{ $t("Profile Setup") }}
        </div>

    </div>

    <div class="bg-white rounded-2xl shadow-md p-4 md:p-6">
      <div class="overflow-x-auto">
        <div class="min-w-full">
          <div class="flex justify-end mb-6">
            <Button
                v-if="!isEditing"
                :show="true"
                :title="$t('Edit')"
                :width="'w-40'"
                @click="startEditing"
            />

            <div v-else class="flex gap-2 mt-2">

                <OutlineButton
                    :show="true"
                    title="Cancel"
                    @click="cancelEditing"
                />

                <Button
                    v-if="isEditing"
                    :show="true"
                    :title="$t('Save')"
                    :width="'w-40'"
                    @click="saveAnswers"
                />
                <LanguageDropdown
                    v-model="currentLang"
                    @changed="onLanguageChanged"
                />
            </div>
          </div>

          <div class="grid grid-cols-1 gap-4">
            <section
              v-for="item in items"
              :key="item.key"
              class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm"
            >
                <div class="size-lf-stretch justify-start text-[#002d45] text-md font-medium leading-[18.20px]" :class="$attrs.labelClass">
                    {{ $t(item.question) }}
                </div>

              <div v-if="!isEditing" class="mt-2">
                <p class="self-stretch justify-start text-[#002d45] text-base leading-tight">
                  {{ item.answer?.trim() ? item.answer : $t("No answer provided yet") }}
                </p>
              </div>

              <div v-else class="mt-5">
                <TextInput
                    :label="item.question"
                    :placeholder="$t('write')"
                    required
                    :minLength="5"
                    :maxLength="100"
                    validationType="text"
                    v-model="draft[item.key]"
                    :isTextarea="true"
                    :helpText="$t('Maximum 100 Characters')"
                />
              </div>
            </section>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, inject, onUnmounted } from "vue";
import axios from "axios";
import Button from "@/components/buttons/Button.vue";
import OutlineButton from "@/components/buttons/OutlineButton.vue";
import TextInput from "@/components/form/TextInput.vue";
import { trans } from 'laravel-vue-i18n';
import { useNotify } from '@/composables/useNotification';
import LanguageDropdown from "@/components/form/LanguageDropdown.vue";
const emitter = inject("emitter");
import { useUserStore } from "@/stores/useUserStore";
const store = useUserStore();

const notify = useNotify();

const currentLang = ref(store.user?.language ?? 'de');
const questions = [
  { key: "thoughts_feelings",    question: "Thought / Feeling" },
  { key: "self_esteem_identity", question: "Self-esteem / Identity" },
  { key: "sleep_relaxation",     question: "Sleep / Relaxation" },
  { key: "work_performance",     question: "Work / Performance" },
  { key: "relationships",        question: "Relationship" },
];

const items = ref(questions.map(i => ({ ...i, answer: "" })));
const isEditing = ref(false);
const draft = ref(Object.fromEntries(questions.map(i => [i.key, ""])));
const errors = ref(Object.fromEntries(questions.map(i => [i.key, ""])));

function startEditing() {
  draft.value = Object.fromEntries(items.value.map(i => [i.key, i.answer || ""]));
  isEditing.value = true;
}

function cancelEditing() {
  isEditing.value = false;
  errors.value = Object.fromEntries(questions.map(i => [i.key, ""]));
}

function onLanguageChanged(v) {
  currentLang.value = v;
  fetchAnswers(v);
}

async function fetchAnswers(lang = currentLang.value) {
  try {
    const { data } = await axios.get(
      route('users.profile.question'),
      { headers: { 'X-Request-Language': lang } }
    );
    const answers = data?.data || data;

    items.value = questions.map(i => ({ ...i, answer: answers?.[i.key] ?? '' }));

    if (isEditing.value) {
      draft.value = Object.fromEntries(
        questions.map(i => [i.key, (answers?.[i.key] ?? '').toString()])
      );
    }
  } catch (e) {
    console.error(e.message);
  }
}

async function saveAnswers() {
  try {
    const payload = {};
    for (const { key } of questions) payload[key] = (draft.value[key] || "").trim();

    payload.language = currentLang.value;

    await axios.put(route('update.users.profile.question'), payload);
    notify.success({
        message: trans('profile_setup_updated'),
    });

    items.value = questions.map(i => ({ ...i, answer: payload[i.key] || "" }));
    isEditing.value = false;
  } catch (e) {
    console.error(e);
  } finally {

  }
}

onMounted(() => fetchAnswers(currentLang.value));

emitter.on("languageChanged", async () => {
    await fetchAnswers(store.user?.language ?? 'de');
});

onUnmounted(() => {
    emitter.off("languageChanged");
});
</script>
