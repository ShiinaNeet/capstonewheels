<template>
    <div class="mx-5 mb-2 px-2.5 pb-2.5 bg-white rounded">
        <va-data-table
        id="data-table"
        :items="eventsList"
        :columns="news.tblColumns"
        :per-page="$root.config.tblPerPage"
        :current-page="$root.config.tblCurrPage"
        no-data-html="No news or event(s) to show"
        :filter="filter"
        @filtered="filtered = $event.items"
        animated
        >
            <template #headerAppend>
                <tr class="table-crud__slot">
                    <th
                    v-for="key in Object.keys(createEvent.data)"
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
                        @click="createEvent.modal = !createEvent.modal"
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
                @click="editEvent.data = { ...rowData }, editEvent.modal = !editEvent.modal"
                />
                <va-button
                class="mb-2 mr-2 hover:opacity-[0.65!important]"
                title="Toggle Status"
                preset="plain"
                :icon="rowData.deleted_at ? 'lock' : 'lock_open'"
                :disabled="rowData.deleted_at ? true : false"
                @click="editEvent.data = { ...rowData }, editEvent.statusModal = !editEvent.statusModal"
                />
                <va-button
                class="mb-2 mr-2 hover:opacity-[0.65!important]"
                title="Delete"
                preset="plain"
                icon="delete"
                @click="editEvent.data = { ...rowData }, editEvent.deleteModal = !editEvent.deleteModal"
                />
            </template>

            <template #bodyAppend>
                <tr v-if="$root.tblPagination(eventsList)">
                    <td
                    id="pagination"
                    :colspan="news.tblColumns.length"
                    >
                        <div class="flex pt-[10px] select-none">
                            <va-pagination
                            class="justify-center"
                            v-model="$root.config.tblCurrPage"
                            :pages="filter == '' ? $root.tblPagination(eventsList) : (pages, $root.config.tblCurrPage = 1)"
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
    v-model="createEvent.modal"
    noOutsideDismiss
    noPadding
    >
        <template #content>
            <div class="w-[410px] p-5">
                <div class="va-title mb-3">
                    Add Event
                </div>
                <va-input
                v-model="createEvent.data.title"
                label="Title"
                requiredMark
                class="w-full mb-2"
                maxlength="120"
                :rules="[(v) => v && v.length > 0 || 'The title field is required.']"
                :error="createEvent.titleEmpty"
                :error-messages="'The title field is required.'"
                @keyup="createEvent.titleEmpty = false"
                />
                <va-input
                type="textarea"
                v-model="createEvent.data.description"
                label="Description"
                requiredMark
                class="w-full mb-2 force-noresize"
                maxlength="255"
                :counter="!!(createEvent.data.description && createEvent.data.description.length > 0)"
                :max-length=255
                :rules="[(v) => v && v.length > 0 || 'The description field is required.']"
                :error="createEvent.descEmpty"
                :error-messages="'The description field is required.'"
                @keyup="createEvent.descEmpty = false"
                :min-rows="10"
                :max-rows="10"
                />
                <div class="flex w-full gap-x-3 mt-[15px]">
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        preset="secondary"
                        @click="
                            createEvent.data = { ...createEvent.resetData },
                            createEvent.titleEmpty = false,
                            createEvent.descEmpty = false,
                            createEvent.saved = false,
                            createEvent.modal = !createEvent.modal
                        ">
                            <p class="font-normal">Cancel</p>
                        </va-button>
                    </div>
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        icon="save"
                        :loading="createEvent.saved"
                        :disabled="createEvent.saved"
                        @click="createEvent.saved = true, insertUpdateEvent('create')"
                        >
                            <p class="font-normal">Save</p>
                        </va-button>
                    </div>
                </div>
            </div>
        </template>
    </va-modal>

    <va-modal
    v-model="editEvent.modal"
    noOutsideDismiss
    noPadding
    >
        <template #content>
            <div class="w-[410px] p-5">
                <div class="va-title mb-3">
                    Edit Event
                </div>
                <va-input
                v-model="editEvent.data.title"
                label="Title"
                requiredMark
                class="w-full mb-2"
                maxlength="120"
                :rules="[(v) => v && v.length > 0 || 'The title field is required.']"
                :error="editEvent.titleEmpty"
                @keyup="editEvent.titleEmpty = false"
                />
                <va-input
                type="textarea"
                v-model="editEvent.data.description"
                label="Description"
                requiredMark
                class="w-full mb-2 force-noresize"
                maxlength="255"
                :counter="!!(editEvent.data.description && editEvent.data.description.length > 0)"
                :max-length=255
                :rules="[(v) => v && v.length > 0 || 'The description field is required.']"
                :error="editEvent.descEmpty"
                @keyup="editEvent.descEmpty = false"
                :min-rows="10"
                :max-rows="10"
                />
                <div class="flex w-full gap-x-3 mt-[15px]">
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        preset="secondary"
                        @click="
                            editEvent.data = { ...createEvent.resetData },
                            editEvent.titleEmpty = false,
                            editEvent.descEmpty = false,
                            editEvent.saved = false,
                            editEvent.modal = !editEvent.modal
                        ">
                            <p class="font-normal">Cancel</p>
                        </va-button>
                    </div>
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        icon="save"
                        :loading="editEvent.saved"
                        :disabled="editEvent.saved"
                        @click="editEvent.saved = true, insertUpdateEvent('save')"
                        >
                            <p class="font-normal">Save</p>
                        </va-button>
                    </div>
                </div>
            </div>
        </template>
    </va-modal>

    <va-modal
    v-model="editEvent.deleteModal"
    @cancel="editEvent.data = { ...createEvent.resetData }"
    noPadding
    >
        <template #content>
            <div class="w-[410px] p-5">
                <div class="va-title mb-3">
                    Delete Event
                </div>
                <va-alert color="warning">
                    <template #icon>
                        <va-icon name="warning" />
                    </template>
                    This action is irreversible
                </va-alert>
                <va-input
                type="textarea"
                :model-value="editEvent.data.title"
                class="w-full mb-2 force-noresize"
                readonly
                autosize
                />
                <div class="flex w-full gap-x-3 mt-[15px]">
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        preset="secondary"
                        @click="editEvent.data = { ...createEvent.resetData }, editEvent.deleteModal = !editEvent.deleteModal"
                        >
                            <p class="font-normal">Cancel</p>
                        </va-button>
                    </div>
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        icon="check"
                        :loading="editEvent.saved"
                        :disabled="editEvent.saved"
                        @click="editEvent.saved = true, deleteNewsEvent()"
                        >
                            <p class="font-normal">Confirm</p>
                        </va-button>
                    </div>
                </div>
            </div>
        </template>
    </va-modal>

    <va-modal
    v-model="editEvent.statusModal"
    @cancel="editEvent.data = { ...createEvent.resetData }"
    noPadding
    >
        <template #content>
            <div class="w-[410px] p-5">
                <div class="va-title mb-3">
                    Status
                </div>
                <va-alert color="warning">
                    <template #icon>
                        <va-icon name="warning" />
                    </template>
                    This action is currently irreversible
                </va-alert>
                <va-input
                type="textarea"
                :model-value="editEvent.data.title"
                class="w-full mb-2 force-noresize"
                readonly
                autosize
                />
                <div class="flex w-full gap-x-3 mt-[15px]">
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        preset="secondary"
                        @click="editEvent.data = { ...createEvent.resetData }, editEvent.statusModal = !editEvent.statusModal"
                        >
                            <p class="font-normal">Cancel</p>
                        </va-button>
                    </div>
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        :icon="!editEvent.data.deleted_at ? 'lock' : 'lock_open'"
                        :loading="editEvent.saved"
                        :disabled="editEvent.saved"
                        @click="editEvent.saved = true, toggleNewsStatus()"
                        >
                            <p class="font-normal">{{ !editEvent.data.deleted_at ? "Deactivate" : "Activate" }}</p>
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

const newEvent = {
    title: "",
    deleted_at: null,
    description: "",
    created_at: formatDate(new Date().getTime(), 'YYYY-MM-DD HH:mm:ss'),
    id: null,
};

export default {
    data () {
        const news = {
            tblColumns: [
                { key: "title", label: "News", sortable: true },
                { key: "deleted_at", label: "Status", width: 50, sortable: false },
                { key: "description", label: "Description", width: 80, tdAlign: "center", sortable: false },
                { key: "created_at", label: "Created On", width: 125, sortable: true },
                { key: "id", label: "Action", width: 60, sortable: false }
            ]
        };

        return {
            news,
            eventsList: [],
            event: {},
            createEvent: {
                modal: false,
                titleEmpty: false,
                descEmpty: false,
                saved: false,
                resetData: { ...newEvent },
                data: { ...newEvent }
            },
            editEvent: {
                modal: false,
                deleteModal: false,
                statusModal: false,
                titleEmpty: false,
                descEmpty: false,
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
        this.getNewsEvent();
    },
    methods: {
        toggleNewsStatus() {
            axios({
                method: 'POST',
                type: 'JSON',
                url: '/news/disable',
                data: { id: this.editEvent.data.id }
            }).then(response => {
                if (response.data.status == 1) {
                    this.$root.prompt(response.data.text);
                    this.editEvent.data = {};
                    this.editEvent.statusModal = false;
                    this.editEvent.saved = false;

                    this.getNewsEvent();
                } else this.$root.prompt(response.data.text);
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
            });
        },
        deleteNewsEvent() {
            axios({
                method: 'POST',
                type: 'JSON',
                url: '/news/delete',
                data: { id: this.editEvent.data.id }
            }).then(response => {
                if (response.data.status == 1) {
                    this.$root.prompt(response.data.text);
                    this.editEvent.data = {};
                    this.editEvent.deleteModal = false;
                    this.editEvent.saved = false;

                    this.getNewsEvent();

                    this.activePreviewRow && this.activePreviewRow.toggleRowDetails(false);
                    this.activePreviewRow = null;
                } else this.$root.prompt(response.data.text);
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
            });
        },
        insertUpdateEvent(method) {
            if (method !== 'create' || method !== 'save') {
                axios({
                    method: 'POST',
                    type: 'JSON',
                    url: '/news/save',
                    data: method === 'create' ? this.createEvent.data : (method === 'save' && this.editEvent.data)
                }).then(response => {
                    if (response.data.status == 1) {
                        this.$root.prompt(response.data.text);
                        method === 'create' ? (
                            this.createEvent.data = { ...newEvent },
                            this.createEvent.modal = false,
                            this.createEvent.titleEmpty = false,
                            this.createEvent.descEmpty = false,
                            this.createEvent.saved = false
                        )
                        : (
                            method === 'save' && (
                                this.editEvent.data = {},
                                this.editEvent.modal = false,
                                this.editEvent.titleEmpty = false,
                                this.editEvent.descEmpty = false,
                                this.editEvent.saved = false
                            )
                        );

                        this.getNewsEvent();
                    } else this.$root.prompt(response.data.text);
                }).catch(error => {
                    // this.$root.prompt(error.response.data.message);
                    let resDataError = Object.keys(error.response.data.errors);

                    if (resDataError.filter(key => key == 'title').length) {
                        method === 'create' ? this.createEvent.titleEmpty = true
                        : (
                            method === 'save' && (this.editEvent.titleEmpty = true)
                        );
                    }
                    if (resDataError.filter(key => key == 'description').length) {
                        method === 'create' ? this.createEvent.descEmpty = true
                        : (
                            method === 'save' && (this.editEvent.descEmpty = true)
                        );
                    }

                    method === 'create' ? this.createEvent.saved = false
                    : (method === 'save' && (this.editEvent.saved = false));
                });
            } else this.$root.prompt();
        },
        getNewsEvent() {
            axios({
                method: 'GET',
                type: 'JSON',
                url: '/news'
            }).then(response => {
                if (response.data.status == 1) {
                    this.eventsList = response.data.result;
                } else this.$root.prompt(response.data.text);
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
            });
        },
        formatDate
    }
}
</script>
