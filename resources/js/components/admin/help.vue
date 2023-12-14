<template>
    <div class="mx-5 mb-2 px-2.5 pb-2.5 bg-white rounded">
        <va-data-table
        id="data-table"
        :items="faqsList"
        :columns="faq.tblColumns"
        :per-page="$root.config.tblPerPage"
        :current-page="$root.config.tblCurrPage"
        no-data-html="No faq(s) to show"
        :filter="filter"
        @filtered="filtered = $event.items"
        animated
        >
            <template #headerAppend>
                <tr class="table-crud__slot">
                    <th
                    v-for="key in Object.keys(createHelp.data)"
                    :key="key"
                    class="py-1 pr-1"
                    >
                        <va-input
                        v-if="key.includes('question')"
                        v-model="filter"
                        placeholder="Search..."
                        >
                            <template #appendInner>
                                <va-icon name="search" color="#C2C2C2" />
                            </template>
                        </va-input>
                        <va-button
                        v-if="key.includes('id')"
                        preset="primary"
                        icon="post_add"
                        @click="createHelp.modal = !createHelp.modal"
                        >
                            Add
                        </va-button>
                    </th>
                </tr>
            </template>

            <template #cell(question)="{ value }">
                {{ value }}
            </template>
            <template #cell(answer)="{ row, isExpanded }">
                <va-button
                class="w-full"
                preset="secondary"
                size="small"
                :icon="isExpanded ? 'va-arrow-up': 'va-arrow-down'"
                @click="row.toggleRowDetails(), activePreviewRow = row"
                >
                    {{ !isExpanded ? 'Show': 'Hide' }}
                </va-button>
            </template>
            <template #expandableRow="{ rowData }">
                <div
                class="p-2"
                id="table-row-desc"
                >
                    <va-input
                    type="textarea"
                    :model-value="rowData.answer"
                    placeholder="No answer available"
                    readonly
                    autosize
                    outline
                    />
                </div>
            </template>
            <template #cell(deleted_at)="{ value }">
                <va-badge
                :text="value ? 'Inactive' : 'Active'"
                :color="value ? 'warning' : 'success'"
                />
            </template>
            <template #cell(created_at)="{ value }">
                {{ formatDate(value, 'MMM. Do YYYY', 'Invalid Date') }}
            </template>
            <template #cell(id)="{ rowData }">
                <va-button
                class="mb-2 mr-2 hover:opacity-[0.65!important]"
                title="Edit"
                preset="plain"
                icon="edit"
                @click="editHelp.data = { ...rowData }, editHelp.modal = !editHelp.modal"
                />
                <va-button
                class="mb-2 mr-2 hover:opacity-[0.65!important]"
                title="Toggle Status"
                preset="plain"
                :icon="rowData.deleted_at ? 'lock' : 'lock_open'"
                :disabled="rowData.deleted_at ? true : false"
                @click="editHelp.data = { ...rowData }, editHelp.statusModal = !editHelp.statusModal"
                />
                <va-button
                class="mb-2 mr-2 hover:opacity-[0.65!important]"
                title="Delete"
                preset="plain"
                icon="delete"
                @click="editHelp.data = { ...rowData }, editHelp.deleteModal = !editHelp.deleteModal"
                />
            </template>

            <template #bodyAppend>
                <tr v-if="$root.tblPagination(faqsList)">
                    <td
                    id="pagination"
                    :colspan="faq.tblColumns.length"
                    >
                        <div class="flex pt-[10px] select-none">
                            <va-pagination
                            class="justify-center"
                            v-model="$root.config.tblCurrPage"
                            :pages="filter == '' ? $root.tblPagination(faqsList) : (pages, $root.config.tblCurrPage = 1)"
                            input
                            />
                        </div>
                    </td>
                </tr>
            </template>
        </va-data-table>
    </div>
    <div class="mx-5 mb-5">
        <div class="va-title text-neutral-400">
            Input fields with
            <span class="text-sm leading-[0.25rem]">*</span>
            are required
        </div>
    </div>

    <va-modal
    v-model="createHelp.modal"
    noOutsideDismiss
    noPadding
    >
        <template #content>
            <div class="w-[410px] p-5">
                <div class="va-title mb-3">
                    Add Help
                </div>
                <va-input
                v-model="createHelp.data.question"
                label="Question *"
                class="w-full mb-2"
                maxlength="120"
                :rules="[(v) => v && v.length > 0 || 'The question field is required.']"
                :error="createHelp.questionEmpty"
                :error-messages="'The question field is required.'"
                @keyup="createHelp.questionEmpty = false"
                />
                <va-input
                type="textarea"
                v-model="createHelp.data.answer"
                label="Answer *"
                class="w-full mb-2 force-noresize"
                maxlength="255"
                :counter="!!(createHelp.data.answer && createHelp.data.answer.length > 0)"
                :max-length=255
                :rules="[(v) => v && v.length > 0 || 'The answer field is required.']"
                :error="createHelp.answerEmpty"
                :error-messages="'The answer field is required.'"
                @keyup="createHelp.answerEmpty = false"
                :min-rows="10"
                :max-rows="10"
                />
                <div class="flex w-full gap-x-3 mt-[15px]">
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        preset="secondary"
                        @click="
                            createHelp.data = { ...createHelp.resetData },
                            createHelp.questionEmpty = false,
                            createHelp.answerEmpty = false,
                            createHelp.saved = false,
                            createHelp.modal = !createHelp.modal
                        "
                        >
                            <p class="font-normal">Cancel</p>
                        </va-button>
                    </div>
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        icon="save"
                        :loading="createHelp.saved"
                        :disabled="createHelp.saved"
                        @click="createHelp.saved = true, insertUpdateHelp('create')"
                        >
                            <p class="font-normal">Save</p>
                        </va-button>
                    </div>
                </div>
            </div>
        </template>
    </va-modal>

    <va-modal
    v-model="editHelp.modal"
    noOutsideDismiss
    noPadding
    >
        <template #content>
            <div class="w-[410px] p-5">
                <div class="va-title mb-3">
                    Edit Help
                </div>
                <va-input
                v-model="editHelp.data.question"
                label="Question *"
                class="w-full mb-2"
                maxlength="120"
                :rules="[(v) => v && v.length > 0 || 'The question field is required.']"
                :error="editHelp.questionEmpty"
                @keyup="editHelp.questionEmpty = false"
                />
                <va-input
                type="textarea"
                v-model="editHelp.data.answer"
                label="Answer *"
                class="w-full mb-2 force-noresize"
                maxlength="255"
                :counter="!!(editHelp.data.answer || editHelp.data.answer.length > 0)"
                :max-length=255
                :rules="[(v) => v && v.length > 0 || 'The answer field is required.']"
                :error="editHelp.answerEmpty"
                @keyup="editHelp.answerEmpty = false"
                :min-rows="10"
                :max-rows="10"
                />
                <div class="flex w-full gap-x-3 mt-[15px]">
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        preset="secondary"
                        @click="
                            editHelp.data = { ...createHelp.resetData },
                            editHelp.questionEmpty = false,
                            editHelp.answerEmpty = false,
                            editHelp.saved = false,
                            editHelp.modal = !editHelp.modal
                        ">
                            <p class="font-normal">Cancel</p>
                        </va-button>
                    </div>
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        icon="save"
                        :loading="editHelp.saved"
                        :disabled="editHelp.saved"
                        @click="editHelp.saved = true, insertUpdateHelp('save')"
                        >
                            <p class="font-normal">Save</p>
                        </va-button>
                    </div>
                </div>
            </div>
        </template>
    </va-modal>

    <va-modal
    v-model="editHelp.deleteModal"
    @cancel="editHelp.data = { ...createHelp.resetData }"
    noPadding
    >
        <template #content>
            <div class="w-[410px] p-5">
                <div class="va-title mb-3">
                    Delete Help
                </div>
                <va-alert color="warning">
                    <template #icon>
                        <va-icon name="warning" />
                    </template>
                    This action is irreversible
                </va-alert>
                <va-input
                type="textarea"
                :model-value="editHelp.data.question"
                class="w-full mb-2 force-noresize"
                readonly
                autosize
                />
                <div class="flex w-full gap-x-3 mt-[15px]">
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        preset="secondary"
                        @click="editHelp.data = { ...createHelp.resetData }, editHelp.deleteModal = !editHelp.deleteModal"
                        >
                            <p class="font-normal">Cancel</p>
                        </va-button>
                    </div>
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        icon="check"
                        :loading="editHelp.saved"
                        :disabled="editHelp.saved"
                        @click="editHelp.saved = true, deleteHelp()"
                        >
                            <p class="font-normal">Confirm</p>
                        </va-button>
                    </div>
                </div>
            </div>
        </template>
    </va-modal>

    <va-modal
    v-model="editHelp.statusModal"
    @cancel="editHelp.data = { ...createHelp.resetData }"
    noPadding
    >
        <template #content>
            <div class="w-[410px] p-5">
                <div class="va-title mb-3">
                    Help Status
                </div>
                <va-alert color="warning">
                    <template #icon>
                        <va-icon name="warning" />
                    </template>
                    This action is currently irreversible
                </va-alert>
                <va-input
                type="textarea"
                :model-value="editHelp.data.question"
                class="w-full mb-2 force-noresize"
                readonly
                autosize
                />
                <div class="flex w-full gap-x-3 mt-[15px]">
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        preset="secondary"
                        @click="editHelp.data = { ...createHelp.resetData }, editHelp.statusModal = !editHelp.statusModal"
                        >
                            <p class="font-normal">Cancel</p>
                        </va-button>
                    </div>
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        :icon="!editHelp.data.deleted_at ? 'lock' : 'lock_open'"
                        :loading="editHelp.saved"
                        :disabled="editHelp.saved"
                        @click="editHelp.saved = true, toggleHelpStatus()"
                        >
                            <p class="font-normal">{{ !editHelp.data.deleted_at ? "Deactivate" : "Activate" }}</p>
                        </va-button>
                    </div>
                </div>
            </div>
        </template>
    </va-modal>
</template>

<style lang="scss" scoped>
.table-crud {
    --va-form-element-default-width: 0;

    .va-input {
        display: block;
    }

    &__slot {
        th {
            vertical-align: middle;
        }
    }
}
</style>

<script>
import formatDate from '@/functions/formatdate.js';

const newHelp = {
    question: "",
    deleted_at: null,
    answer: "",
    created_at: formatDate(new Date().getTime(), 'YYYY-MM-DD HH:mm:ss'),
    id: null,
};

export default {
    data () {
        const faq = {
            tblColumns: [
                { key: "question", label: "Help", width: 90, sortable: true },
                { key: "deleted_at", label: "Status", width: 90, sortable: false },
                { key: "answer", label: "Answer", width: 80, tdAlign: "center", sortable: false },
                { key: "created_at", label: "Created On", width: 125, sortable: true },
                { key: "id", label: "Action", width: 60, sortable: false }
            ]
        };

        return {
            faq,
            faqsList: [],
            help: {},
            createHelp: {
                modal: false,
                questionEmpty: false,
                answerEmpty: false,
                saved: false,
                resetData: { ...newHelp },
                data: { ...newHelp }
            },
            editHelp: {
                modal: false,
                deleteModal: false,
                statusModal: false,
                questionEmpty: false,
                answerEmpty: false,
                saved: false,
                data: {}
            },
            activePreviewRow: null,
            filtered: null,
            filter: ""
        };
    },
    computed: {
        pages() {
            return this.$root.config.tblPerPage && this.$root.config.tblPerPage !== 0
            ? Math.ceil(this.filtered.length / this.$root.config.tblPerPage)
            : this.filtered.length;
        },
    },
    mounted () {
        this.getFAQS();
    },
    methods: {
        toggleHelpStatus() {
            axios({
                method: 'POST',
                type: 'JSON',
                url: '/faq/disable',
                data: { id: this.editHelp.data.id }
            }).then(response => {
                if (response.data.status == 1) {
                    this.$root.prompt(response.data.text);
                    this.editHelp.data = { ...newHelp };
                    this.editHelp.statusModal = false;
                    this.editHelp.saved = false;

                    this.getFAQS();
                } else this.$root.prompt(response.data.text);
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
            });
        },
        deleteHelp() {
            axios({
                method: 'POST',
                type: 'JSON',
                url: '/faq/delete',
                data: { id: this.editHelp.data.id }
            }).then(response => {
                if (response.data.status == 1) {
                    this.$root.prompt(response.data.text);
                    this.editHelp.data = { ...newHelp };
                    this.editHelp.deleteModal = false;
                    this.editHelp.saved = false;

                    this.getFAQS();

                    this.activePreviewRow && this.activePreviewRow.toggleRowDetails(false);
                    this.activePreviewRow = null;
                } else this.$root.prompt(response.data.text);
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
            });
        },
        insertUpdateHelp(method) {
            if (method !== 'create' || method !== 'save') {
                axios({
                    method: 'POST',
                    type: 'JSON',
                    url: '/faq/save',
                    data: method === 'create' ? this.createHelp.data : (method === 'save' && this.editHelp.data)
                }).then(response => {
                    if (response.data.status == 1) {
                        this.$root.prompt(response.data.text);
                        method === 'create' ? (
                            this.createHelp.data = { ...newHelp },
                            this.createHelp.modal = false,
                            this.createHelp.questionEmpty = false,
                            this.createHelp.answerEmpty = false,
                            this.createHelp.saved = false
                        )
                        : (
                            method === 'save' && (
                                this.editHelp.data = {},
                                this.editHelp.modal = false,
                                this.editHelp.questionEmpty = false,
                                this.editHelp.answerEmpty = false,
                                this.editHelp.saved = false
                            )
                        );

                        this.getFAQS();
                    } else this.$root.prompt(response.data.text);
                }).catch(error => {
                    // this.$root.prompt(error.response.data.message);
                    let resDataError = Object.keys(error.response.data.errors);

                    if (resDataError.filter(key => key == 'question').length) {
                        method === 'create' ? this.createHelp.questionEmpty = true
                        : (
                            method === 'save' && (this.editHelp.questionEmpty = true)
                        );
                    }
                    if (resDataError.filter(key => key == 'answer').length) {
                        method === 'create' ? this.createHelp.answerEmpty = true
                        : (
                            method === 'save' && (this.editHelp.answerEmpty = true)
                        );
                    }

                    method === 'create' ? this.createHelp.saved = false
                    : (method === 'save' && (this.editHelp.saved = false));
                });
            } else this.$root.prompt();
        },
        getFAQS() {
            axios({
                method: 'GET',
                type: 'JSON',
                url: '/faqs'
            }).then(response => {
                if (response.data.status == 1) {
                    this.faqsList = response.data.result;
                } else this.$root.prompt(response.data.text);
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
            });
        },
        formatDate
    }
}
</script>
