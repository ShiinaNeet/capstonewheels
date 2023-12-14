<template>
    <div class="mx-5 mb-2 px-2.5 pb-2.5 bg-white rounded">
        <va-data-table
        id="data-table"
        :items="requirements"
        :columns="req.tblColumns.filter(item => { return $root.auth.userType === 1 ? (item.key !== 'id') : item })"
        :per-page="$root.config.tblPerPage"
        :current-page="$root.config.tblCurrPage"
        no-data-html="No requirement(s) to show"
        :filter="filter"
        @filtered="filtered = $event.items"
        animated
        >
            <template #headerAppend>
                <tr class="table-crud__slot">
                    <th
                    v-for="key in Object.keys(createReq.data).filter(item => { return $root.auth.userType === 1 ? (item !== 'id') : item })"
                    :key="key"
                    class="py-1 pr-1"
                    >
                        <va-input
                        v-if="key.includes('name')"
                        v-model="filter"
                        placeholder="Search..."
                        >
                            <template #appendInner>
                                <va-icon name="search" color="#C2C2C2" />
                            </template>
                        </va-input>
                        <va-button
                        v-if="key.includes('id') && $root.auth.userType !== 1"
                        preset="primary"
                        icon="post_add"
                        @click="createReq.modal = !createReq.modal"
                        >
                            Add
                        </va-button>
                        <va-button
                        v-if="key.includes('created_at') && $root.auth.userType === 1"
                        class="invisible"
                        preset="primary"
                        disabled
                        />
                    </th>
                </tr>
            </template>

            <template #cell(name)="{ value }">
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
                class="p-2"
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
                @click="editReq.data = { ...rowData }, editReq.modal = !editReq.modal"
                />
                <va-button
                class="mb-2 mr-2 hover:opacity-[0.65!important]"
                title="Delete"
                preset="plain"
                icon="delete"
                @click="editReq.data = { ...rowData }, editReq.deleteModal = !editReq.deleteModal"
                />
            </template>

            <template #bodyAppend>
                <tr v-if="$root.tblPagination(requirements)">
                    <td
                    id="pagination"
                    :colspan="req.tblColumns.length"
                    >
                        <div class="flex pt-[10px] select-none">
                            <va-pagination
                            class="justify-center"
                            v-model="$root.config.tblCurrPage"
                            :pages="filter == '' ? $root.tblPagination(requirements) : (pages, $root.config.tblCurrPage = 1)"
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
    v-model="createReq.modal"
    noOutsideDismiss
    noPadding
    >
        <template #content>
            <div class="w-[410px] p-5">
                <div class="va-title mb-3">
                    Add Requirement
                </div>
                <va-input
                v-model="createReq.data.name"
                label="Name"
                requiredMark
                class="w-full mb-2"
                maxlength="120"
                :rules="[(v) => v && v.length > 0 || 'The name field is required.']"
                :error="createReq.nameEmpty"
                :error-messages="'The name field is required.'"
                @keyup="createReq.nameEmpty = false"
                />
                <va-input
                type="textarea"
                v-model="createReq.data.description"
                label="Description"
                class="w-full mb-2 force-noresize"
                maxlength="255"
                :counter="!!(createReq.data.description && createReq.data.description.length > 0)"
                :max-length=255
                :min-rows="10"
                :max-rows="10"
                />
                <div class="flex w-full gap-x-3 mt-[15px]">
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        preset="secondary"
                        @click="
                            createReq.data = { ...createReq.resetData },
                            createReq.nameEmpty = false,
                            createReq.saved = false,
                            createReq.modal = !createReq.modal
                        ">
                            <p class="font-normal">Cancel</p>
                        </va-button>
                    </div>
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        icon="save"
                        :loading="createReq.saved"
                        :disabled="createReq.saved"
                        @click="createReq.saved = true, insertUpdateRequirement('create')"
                        >
                            <p class="font-normal">Save</p>
                        </va-button>
                    </div>
                </div>
            </div>
        </template>
    </va-modal>

    <va-modal
    v-model="editReq.modal"
    noOutsideDismiss
    noPadding
    >
        <template #content>
            <div class="w-[410px] p-5">
                <div class="va-title mb-3">
                    Edit Requirement
                </div>
                <va-input
                v-model="editReq.data.name"
                label="Name"
                requiredMark
                class="w-full mb-2"
                maxlength="120"
                :rules="[(v) => v && v.length > 0 || 'The name field is required.']"
                :error="editReq.nameEmpty"
                @keyup="editReq.nameEmpty = false"
                />
                <va-input
                type="textarea"
                v-model="editReq.data.description"
                label="Description"
                class="w-full mb-2 force-noresize"
                maxlength="255"
                :counter="!!(editReq.data.description && editReq.data.description.length > 0)"
                :max-length=255
                :min-rows="10"
                :max-rows="10"
                />
                <div class="flex w-full gap-x-3 mt-[15px]">
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        preset="secondary"
                        @click="
                            editReq.data = { ...createReq.resetData },
                            editReq.nameEmpty = false,
                            editReq.saved = false,
                            editReq.modal = !editReq.modal"
                        >
                            <p class="font-normal">Cancel</p>
                        </va-button>
                    </div>
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        icon="save"
                        :loading="editReq.saved"
                        :disabled="editReq.saved"
                        @click="editReq.saved = true, insertUpdateRequirement('save')"
                        >
                            <p class="font-normal">Save</p>
                        </va-button>
                    </div>
                </div>
            </div>
        </template>
    </va-modal>

    <va-modal
    v-model="editReq.deleteModal"
    @cancel="editReq.data = { ...createReq.resetData }"
    noPadding
    >
        <template #content>
            <div class="w-[410px] p-5">
                <div class="va-title mb-3">
                    Delete Requirement
                </div>
                <va-alert color="warning">
                    <template #icon>
                        <va-icon name="warning" />
                    </template>
                    This action is irreversible
                </va-alert>
                <va-input
                type="textarea"
                :model-value="editReq.data.name"
                class="w-full mb-2 force-noresize"
                readonly
                autosize
                />
                <div class="flex w-full gap-x-3 mt-[15px]">
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        preset="secondary"
                        @click="editReq.data = { ...createReq.resetData }, editReq.deleteModal = !editReq.deleteModal"
                        >
                            <p class="font-normal">Cancel</p>
                        </va-button>
                    </div>
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        icon="check"
                        :loading="editReq.saved"
                        :disabled="editReq.saved"
                        @click="editReq.saved = true, deleteRequirement()"
                        >
                            <p class="font-normal">Confirm</p>
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

const newReq = {
    name: "",
    description: "",
    created_at: formatDate(new Date().getTime(), 'YYYY-MM-DD HH:mm:ss'),
    id: null,
};

export default {
    data () {
        const req = {
            tblColumns: [
                { key: "name", label: "Requirement", sortable: true },
                { key: "description", label: "Description", width: 80, tdAlign: "center", sortable: false },
                { key: "created_at", label: "Created On", width: 125, sortable: true },
                { key: "id", label: "Action", width: 60, sortable: false }
            ]
        };

        return {
            req,
            requirements: [],
            requirement: {},
            createReq: {
                modal: false,
                nameEmpty: false,
                saved: false,
                resetData: { ...newReq },
                data: { ...newReq }
            },
            editReq: {
                modal: false,
                deleteModal: false,
                nameEmpty: false,
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
        this.getRequirements();
    },
    methods: {
        deleteRequirement() {
            axios({
                method: 'POST',
                type: 'JSON',
                url: '/requirement/delete',
                data: { id: this.editReq.data.id }
            }).then(response => {
                if (response.data.status == 1) {
                    this.$root.prompt(response.data.text);
                    this.editReq.data = { ...newReq };
                    this.editReq.deleteModal = false;
                    this.editReq.saved = false;

                    this.getRequirements();

                    this.activePreviewRow && this.activePreviewRow.toggleRowDetails(false);
                    this.activePreviewRow = null;
                } else this.$root.prompt(response.data.text);
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
            });
        },
        insertUpdateRequirement(method) {
            if (method !== 'create' || method !== 'save') {
                axios({
                    method: 'POST',
                    type: 'JSON',
                    url: '/requirement/save',
                    data: method === 'create' ? this.createReq.data : (method === 'save' && this.editReq.data)
                }).then(response => {
                    if (response.data.status == 1) {
                        this.$root.prompt(response.data.text);
                        method === 'create' ? (
                            this.createReq.data = { ...newReq },
                            this.createReq.modal = false,
                            this.createReq.nameEmpty = false,
                            this.createReq.saved = false
                        )
                        : (
                            method === 'save' && (
                                this.editReq.data = { ...newReq },
                                this.editReq.modal = false,
                                this.editReq.nameEmpty = false,
                                this.editReq.saved = false
                            )
                        );

                        this.getRequirements();
                    } else this.$root.prompt(response.data.text);
                }).catch(error => {
                    // this.$root.prompt(error.response.data.message);
                    let resDataError = Object.keys(error.response.data.errors);

                    if (resDataError.filter(key => key == 'name').length) {
                        method === 'create' ? this.createReq.nameEmpty = true
                        : (
                            method === 'save' && (this.editReq.nameEmpty = true)
                        );
                    }

                    method === 'create' ? this.createReq.saved = false
                    : (method === 'save' && (this.editReq.saved = false));
                });
            } else this.$root.prompt();
        },
        getRequirements() {
            axios({
                method: 'GET',
                type: 'JSON',
                url: '/requirements'
            }).then(response => {
                if (response.data.status == 1) {
                    this.requirements = response.data.result;
                } else this.$root.prompt(response.data.text);
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
            });
        },
        formatDate
    }
}
</script>
