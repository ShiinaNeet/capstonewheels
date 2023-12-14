<template>
    <div class="mx-5 mb-2 px-2.5 pb-2.5 bg-white rounded">
        <va-data-table
        id="data-table"
        :items="roomsList"
        :columns="scope.tblColumns.filter(item => { return $root.auth.userType === 1 ? (item.key !== 'id') : item })"
        :per-page="$root.config.tblPerPage"
        :current-page="$root.config.tblCurrPage"
        no-data-html="No room(s) to show"
        :filter="filter"
        @filtered="filtered = $event.items"
        animated
        >
            <template #headerAppend>
                <tr class="table-crud__slot">
                    <th
                    v-for="key in Object.keys(createRoom.data).filter(item => { return $root.auth.userType === 1 ? (item !== 'id') : item })"
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
                        @click="createRoom.modal = !createRoom.modal"
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
                @click="editRoom.data = { ...rowData }, editRoom.modal = !editRoom.modal"
                />
                <va-button
                class="mb-2 mr-2 hover:opacity-[0.65!important]"
                title="Delete"
                preset="plain"
                icon="delete"
                @click="editRoom.data = { ...rowData }, editRoom.deleteModal = !editRoom.deleteModal"
                />
            </template>

            <template #bodyAppend>
                <tr v-if="$root.tblPagination(roomsList)">
                    <td
                    id="pagination"
                    :colspan="scope.tblColumns.length"
                    >
                        <div class="flex pt-[10px] select-none">
                            <va-pagination
                            class="justify-center"
                            v-model="$root.config.tblCurrPage"
                            :pages="filter == '' ? $root.tblPagination(roomsList) : (pages, $root.config.tblCurrPage = 1)"
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
    v-model="createRoom.modal"
    noOutsideDismiss
    noPadding
    >
        <template #content>
            <div class="w-[410px] p-5">
                <div class="va-title mb-3">
                    Add Room
                </div>
                <va-input
                v-model="createRoom.data.name"
                label="Name"
                requiredMark
                class="w-full mb-2"
                maxlength="120"
                :rules="[(v) => v && v.length > 0 || 'The name field is required.']"
                :error="createRoom.nameEmpty"
                :error-messages="'The name field is required.'"
                @keyup="createRoom.nameEmpty = false"
                />
                <va-input
                type="textarea"
                v-model="createRoom.data.description"
                label="Description"
                class="w-full mb-2 force-noresize"
                maxlength="255"
                :counter="!!(createRoom.data.description && createRoom.data.description.length > 0)"
                :max-length=255
                :min-rows="10"
                :max-rows="10"
                />
                <div class="flex w-full gap-x-3 mt-[15px]">
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        preset="secondary"
                        @click="
                            createRoom.data = { ...createRoom.resetData },
                            createRoom.nameEmpty = false,
                            createRoom.saved = false,
                            createRoom.modal = !createRoom.modal
                        ">
                            <p class="font-normal">Cancel</p>
                        </va-button>
                    </div>
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        icon="save"
                        :loading="createRoom.saved"
                        :disabled="createRoom.saved"
                        @click="createRoom.saved = true, insertUpdateRoom('create')"
                        >
                            <p class="font-normal">Save</p>
                        </va-button>
                    </div>
                </div>
            </div>
        </template>
    </va-modal>

    <va-modal
    v-model="editRoom.modal"
    noOutsideDismiss
    noPadding
    >
        <template #content>
            <div class="w-[410px] p-5">
                <div class="va-title mb-3">
                    Edit Room
                </div>
                <va-input
                v-model="editRoom.data.name"
                label="Name"
                requiredMark
                class="w-full mb-2"
                maxlength="120"
                :rules="[(v) => v && v.length > 0 || 'The name field is required.']"
                :error="editRoom.nameEmpty"
                @keyup="editRoom.nameEmpty = false"
                />
                <va-input
                type="textarea"
                v-model="editRoom.data.description"
                label="Description"
                class="w-full mb-2 force-noresize"
                maxlength="255"
                :counter="!!(editRoom.data.description && editRoom.data.description.length > 0)"
                :max-length=255
                :min-rows="10"
                :max-rows="10"
                />
                <div class="flex w-full gap-x-3 mt-[15px]">
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        preset="secondary"
                        @click="
                            editRoom.data = { ...createRoom.resetData },
                            editRoom.nameEmpty = false,
                            editRoom.saved = false,
                            editRoom.modal = !editRoom.modal
                        ">
                            <p class="font-normal">Cancel</p>
                        </va-button>
                    </div>
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        icon="save"
                        :loading="editRoom.saved"
                        :disabled="editRoom.saved"
                        @click="editRoom.saved = true, insertUpdateRoom('save')"
                        >
                            <p class="font-normal">Save</p>
                        </va-button>
                    </div>
                </div>
            </div>
        </template>
    </va-modal>

    <va-modal
    v-model="editRoom.deleteModal"
    @cancel="editRoom.data = { ...createRoom.resetData }"
    noPadding
    >
        <template #content>
            <div class="w-[410px] p-5">
                <div class="va-title mb-3">
                    Delete Room
                </div>
                <va-alert color="warning">
                    <template #icon>
                        <va-icon name="warning" />
                    </template>
                    This action is irreversible
                </va-alert>
                <va-input
                type="textarea"
                :model-value="editRoom.data.name"
                class="w-full mb-2 force-noresize"
                readonly
                autosize
                />
                <div class="flex w-full gap-x-3 mt-[15px]">
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        preset="secondary"
                        @click="editRoom.data = { ...createRoom.resetData }, editRoom.deleteModal = !editRoom.deleteModal"
                        >
                            <p class="font-normal">Cancel</p>
                        </va-button>
                    </div>
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        icon="check"
                        :loading="editRoom.saved"
                        :disabled="editRoom.saved"
                        @click="editRoom.saved = true, deleteRoom()"
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

const newRoom = {
    name: "",
    description: "",
    created_at: formatDate(new Date().getTime(), 'YYYY-MM-DD HH:mm:ss'),
    id: null,
};

export default {
    data () {
        const scope = {
            tblColumns: [
                { key: "name", label: "Room", sortable: true },
                { key: "description", label: "Description", width: 80, tdAlign: "center", sortable: false },
                { key: "created_at", label: "Created On", width: 125, sortable: true },
                { key: "id", label: "Action", width: 60, sortable: false }
            ]
        };

        return {
            scope,
            roomsList: [],
            room: {},
            createRoom: {
                modal: false,
                nameEmpty: false,
                saved: false,
                resetData: { ...newRoom },
                data: { ...newRoom }
            },
            editRoom: {
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
        this.getRooms();
    },
    methods: {
        deleteRoom() {
            axios({
                method: 'POST',
                type: 'JSON',
                url: '/room/delete',
                data: { id: this.editRoom.data.id }
            }).then(response => {
                if (response.data.status == 1) {
                    this.$root.prompt(response.data.text);
                    this.editRoom.data = { ...newRoom };
                    this.editRoom.deleteModal = false;
                    this.editRoom.saved = false;

                    this.getRooms();

                    this.activePreviewRow && this.activePreviewRow.toggleRowDetails(false);
                    this.activePreviewRow = null;
                } else this.$root.prompt(response.data.text);
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
            });
        },
        insertUpdateRoom(method) {
            if (method !== 'create' || method !== 'save') {
                axios({
                    method: 'POST',
                    type: 'JSON',
                    url: '/room/save',
                    data: method === 'create' ? this.createRoom.data : (method === 'save' && this.editRoom.data)
                }).then(response => {
                    if (response.data.status == 1) {
                        this.$root.prompt(response.data.text);
                        method === 'create' ? (
                            this.createRoom.data = { ...newRoom },
                            this.createRoom.modal = false,
                            this.createRoom.nameEmpty = false,
                            this.createRoom.saved = false
                        )
                        : (
                            method === 'save' && (
                                this.editRoom.data = { ...newRoom },
                                this.editRoom.modal = false,
                                this.editRoom.nameEmpty = false,
                                this.editRoom.saved = false
                            )
                        );

                        this.getRooms();
                    } else this.$root.prompt(response.data.text);
                }).catch(error => {
                    // this.$root.prompt(error.response.data.message);
                    let resDataError = Object.keys(error.response.data.errors);

                    if (resDataError.filter(key => key == 'name').length) {
                        method === 'create' ? this.createRoom.nameEmpty = true
                        : (
                            method === 'save' && (this.editRoom.nameEmpty = true)
                        );
                    }

                    method === 'create' ? this.createRoom.saved = false
                    : (method === 'save' && (this.editRoom.saved = false));
                });
            } else this.$root.prompt();
        },
        getRooms() {
            axios({
                method: 'GET',
                type: 'JSON',
                url: '/rooms'
            }).then(response => {
                if (response.data.status == 1) {
                    this.roomsList = response.data.result;
                } else this.$root.prompt(response.data.text);
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
            });
        },
        formatDate
    }
}
</script>
