<template>
    <div class="mx-5 mb-2 px-2.5 pb-2.5 bg-white rounded">
        <va-data-table
        id="data-table"
        :items="helpList"
        :columns="help.tblColumns"
        :per-page="$root.config.tblPerPage"
        :current-page="$root.config.tblCurrPage"
        no-data-html="No help record to show"
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
                        v-if="key.includes('title')"
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

            <template #cell(title)="{ value }">
                {{ value }}
            </template>
            <template #cell(description)="{ row, isExpanded }">
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
                class="grid gap-y-2 p-2"
                >
                    <div class="flex w-full gap-x-2 ">
                        <div
                        class="w-[100%]"
                        id="table-row-desc"
                        >
                            <va-input
                            type="textarea"
                            :model-value="rowData.description"
                            placeholder="No description available"
                            readonly
                            autosize
                            outline
                            />
                        </div>
                    </div>
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
                :disabled="rowData.deleted_at"
                />
                <va-button
                class="mb-2 mr-2 hover:opacity-[0.65!important]"
                title="Toggle Status"
                preset="plain"
                :icon="rowData.deleted_at ? 'lock' : 'lock_open'"
                @click="editHelp.data = { ...rowData }, editHelp.statusModal = !editHelp.statusModal"
                />
                <va-button
                class="mb-2 mr-2 hover:opacity-[0.65!important]"
                title="Delete"
                preset="plain"
                icon="delete"
                :disabled="rowData.deleted_at"
                @click="editHelp.data = { ...rowData }, editHelp.deleteModal = !editHelp.deleteModal"
                />
            </template>

            <template #bodyAppend>
                <tr v-if="$root.tblPagination(helpList)">
                    <td
                    id="pagination"
                    :colspan="help.tblColumns.length"
                    >
                        <div class="flex pt-[10px] select-none">
                            <va-pagination
                            class="justify-center"
                            v-model="$root.config.tblCurrPage"
                            :pages="filter == '' ? $root.tblPagination(helpList) : (pages, $root.config.tblCurrPage = 1)"
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
                v-model="createHelp.data.title"
                label="Title"
                requiredMark
                class="w-full mb-2"
                maxlength="120"
                :rules="[(v) => v && v.length > 0 || 'The title field is required.']"
                :error="createHelp.titleEmpty"
                :error-messages="'The title field is required.'"
                @keyup="createHelp.titleEmpty = false"
                />
                <va-select
                v-model="createHelp.data.tags"
                label="Tags"
                requiredMark
                class="w-full mb-2"
                :options="tags"
                searchable
                highlight-matched-text
                :error="createHelp.tagEmpty"
                :error-messages="'The room field is required.'"
                @update:modelValue="createHelp.tagEmpty = false"
                />
                <va-input
                type="textarea"
                v-model="createHelp.data.description"
                label="Description"
                requiredMark
                class="w-full mb-2 force-noresize"
                maxlength="255"
                :counter="!!(createHelp.data.description && createHelp.data.description.length > 0)"
                :max-length=255
                :rules="[(v) => v && v.length > 0 || 'The description field is required.']"
                :error="createHelp.descEmpty"
                :error-messages="'The description field is required.'"
                @keyup="createHelp.descEmpty = false"
                :min-rows="10"
                :max-rows="10"
                />
                <div class="flex w-full gap-x-3 mt-[15px]">
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        preset="secondary"
                        @click="
                            createHelp.data = { ...createHelp.resetData },
                            createHelp.titleEmpty = false,
                            createHelp.descEmpty = false,
                            createHelp.saved = false,
                            createHelp.modal = !createHelp.modal
                        ">
                            <p class="font-normal">Cancel</p>
                        </va-button>
                    </div>
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        icon="save"
                        :loading="createHelp.saved"
                        :disabled="createHelp.saved"
                        @click="createHelp.saved = true, insertUpdateEvent('create')"
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
                v-model="editHelp.data.title"
                label="Title"
                requiredMark
                class="w-full mb-2"
                maxlength="120"
                :rules="[(v) => v && v.length > 0 || 'The title field is required.']"
                :error="editHelp.titleEmpty"
                @keyup="editHelp.titleEmpty = false"
                />
                <va-select
                v-model="editHelp.data.tags"
                label="Tags"
                requiredMark
                class="w-full mb-2"
                :options="tags"
                searchable
                highlight-matched-text
                :error="editHelp.tagEmpty"
                :error-messages="'The room field is required.'"
                @update:modelValue="createHelp.tagEmpty = false"
                />
                <va-input
                type="textarea"
                v-model="editHelp.data.description"
                label="Description"
                requiredMark
                class="w-full mb-2 force-noresize"
                maxlength="255"
                :counter="!!(editHelp.data.description && editHelp.data.description.length > 0)"
                :max-length=255
                :rules="[(v) => v && v.length > 0 || 'The description field is required.']"
                :error="editHelp.descEmpty"
                @keyup="editHelp.descEmpty = false"
                :min-rows="10"
                :max-rows="10"
                />
                <div class="flex w-full gap-x-3 mt-[15px]">
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        preset="secondary"
                        @click="
                            editHelp.data = { ...createHelp.resetData },
                            editHelp.titleEmpty = false,
                            editHelp.descEmpty = false,
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
                        :disabled="editHelp.saved || editHelp.fileNotFound"
                        @click="editHelp.saved = true, insertUpdateEvent('save')"
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
                    Delete Help?
                </div>
                <va-alert color="warning">
                    <template #icon>
                        <va-icon name="warning" />
                    </template>
                    This action is irreversible
                </va-alert>
                <va-input
                type="textarea"
                :model-value="editHelp.data.title"
                class="w-full mb-2 force-noresize"
                readonly
                autosize
                />
                <div class="flex w-full gap-x-3 mt-[15px]">
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        preset="secondary"
                        @click="editHelp.data = { ...createHelp.resetData }, 
                        editHelp.deleteModal = !editHelp.deleteModal"
                        >
                            <p class="font-normal">Cancel</p>
                        </va-button>
                    </div>
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        icon="check"
                        :loading="editHelp.saved"
                        :disabled="editHelp.saved "
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
                    Status
                </div>
                <va-input
                type="textarea"
                :model-value="editHelp.data.title"
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
                        @click="editHelp.saved = true, handleButtonClick()"
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
    title: "",
    deleted_at: null,
    description: "",
    tags:"",
    created_at: formatDate(new Date().getTime(), 'YYYY-MM-DD HH:mm:ss'),
    id: null,
};

export default {
    data () {
        const help = {
            tblColumns: [
                { key: "title", label: "Help",width: "50%", sortable: true },
                { key: "tags", label: "Tags", width: "10%", sortable: false },
                { key: "deleted_at", label: "Status", width: 90, sortable: false},
                { key: "description", label: "Description", width: 80, tdAlign: "center", sortable: false },
                { key: "created_at", label: "Created On", width: 125, sortable: true },
                { key: "id", label: "Action", width: "5%", sortable: false }
            ]
        };

        return {
            help,
            tags: [
                "Enrollment",
                "Payment",
                "Service",
                "Schedule",
                "Others",
            ],
            helpList: [],
            helps: {},
            createHelp: {
                modal: false,
                titleEmpty: false,
                descEmpty: false,
                tagEmpty: false,
                saved: false,
                resetData: { ...newHelp },
                data: { ...newHelp }
            },
            editHelp: {
                modal: false,
                deleteModal: false,
                statusModal: false,
                titleEmpty: false,
                tagEmpty:false,
                descEmpty: false,
                saved: false,
                data: {},
            },
            activePreviewRow: null,
            filtered: null,
            filter: "",
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
        this.getHelpList();
    },
    methods: {
        handleButtonClick() {
            this.editHelp.saved = true;

            if (this.editHelp.data.deleted_at === null) {
                this.toggleHelpStatus();
            } else {
                this.enableHelpStatus();
            }
        },
        toggleHelpStatus() {
            axios({
                method: 'POST',
                type: 'JSON',
                url: '/help/disable',
                data: { id: this.editHelp.data.id }
            }).then(response => {
                if (response.data.status == 1) {
                    this.$root.prompt(response.data.text);
                    this.editHelp.data = {};
                    this.editHelp.statusModal = false;
                    this.editHelp.saved = false;

                    this.getHelpList();
                } else this.$root.prompt(response.data.text);
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
            });
        },
        enableHelpStatus() {
            axios({
                method: 'POST',
                type: 'JSON',
                url: '/help/enable',
                data: { id: this.editHelp.data.id }
            }).then(response => {
                if (response.data.status == 1) {
                    this.$root.prompt(response.data.text);
                    this.editHelp.data = {};
                    this.editHelp.statusModal = false;
                    this.editHelp.saved = false;

                    this.getHelpList();
                } else this.$root.prompt(response.data.text);
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
            });
        },
        deleteHelp() {
            axios({
                method: 'POST',
                type: 'JSON',
                url: '/help/delete',
                data: { id: this.editHelp.data.id }
            }).then(response => {
                if (response.data.status == 1) {
                    this.$root.prompt(response.data.text);
                    this.editHelp.data = {};
                    this.editHelp.deleteModal = false;
                    this.editHelp.saved = false;

                    this.getHelpList();

                    this.activePreviewRow && this.activePreviewRow.toggleRowDetails(false);
                    this.activePreviewRow = null;
                } else this.$root.prompt(response.data.text);
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
            });
        },
        insertUpdateEvent(method) {
            if (method !== 'create' || method !== 'save') {
                let fdata;
                method === 'create' ? (
                    fdata = this.createHelp.data
                    )
                    : (method === 'save') && (
                    fdata = this.editHelp.data
                );
                const data = new FormData();
                data.append('form', JSON.stringify(fdata));
                axios({
                    method: 'POST',
                    type: 'JSON',
                    url: '/help/save',
                    data: data,
                    headers: { 'Content-Type': 'multipart/form-data' }
                }).then(response => {
                    if (response.data.status == 1) {
                        this.$root.prompt(response.data.text);
                        method === 'create' ? (
                            this.createHelp.data = { ...newHelp },
                            this.createHelp.modal = false,
                            this.createHelp.titleEmpty = false,
                            this.createHelp.descEmpty = false,
                            this.createHelp.saved = false,
                            this.createHelp.tagEmpty = false
                        )
                        : (
                            method === 'save' && (
                                this.editHelp.data = {},
                                this.editHelp.modal = false,
                                this.editHelp.titleEmpty = false,
                                this.editHelp.descEmpty = false,
                                this.editHelp.saved = false,
                                this.editHelp.tagEmpty = false
                            )
                        );

                        this.getHelpList();
                    } else this.$root.prompt(response.data.text);
                }).catch(error => {
                    // this.$root.prompt(error.response.data.message);
                    let resDataError = Object.keys(error.response.data.errors);

                    if (resDataError.filter(key => key == 'title').length) {
                        method === 'create' ? this.createHelp.titleEmpty = true
                        : (
                            method === 'save' && (this.editHelp.titleEmpty = true)
                        );
                    }
                    if (resDataError.filter(key => key == 'description').length) {
                        method === 'create' ? this.createHelp.descEmpty = true
                        : (
                            method === 'save' && (this.editHelp.descEmpty = true)
                        );
                    }

                    method === 'create' ? this.createHelp.saved = false
                    : (method === 'save' && (this.editHelp.saved = false));
                });
            } else this.$root.prompt();
        },
        getHelpList() {
            axios({
                method: 'GET',
                type: 'JSON',
                url: '/help/sort/created_at'
            }).then(response => {
                if (response.data.status == 1) {
                    this.helpList = response.data.result;
                } else this.$root.prompt(response.data.text);
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
            });
        },
        formatDate
    }
}
</script>
