<template>
    <div class="mx-5 mb-2 px-2.5 pb-2.5 bg-white rounded">
        <va-data-table
        id="data-table"
        :items="accounts"
        :columns="acc.tblColumns"
        :per-page="$root.config.tblPerPage"
        :current-page="$root.config.tblCurrPage"
        no-data-html="No account(s) to show"
        :filter="filter"
        @filtered="filtered = $event.items"
        animated
        striped
        >
            <template #headerAppend>
                <tr class="table-crud__slot">
                    <th
                    v-for="key in Object.keys(createAccount.data)"
                    :key="key"
                    class="py-1 pr-1"
                    >
                        <va-input
                        v-if="key.includes('email') && !fmode"
                        v-model="filter"
                        placeholder="Search..."
                        >
                            <template #appendInner>
                                <va-icon
                                name="email"
                                title="Add Account"
                                color="#C2C2C2"
                                @click="fmode = 1"
                                />
                            </template>
                        </va-input>
                        <va-input
                        v-if="key.includes('email') && fmode"
                        v-model="createAccount.data[key]"
                        type="email"
                        :placeholder="acc.createNew[$root.arrayFind(acc.createNew, item => item.key === key)].label + ' *'"
                        :error="createAccount.emailEmpty"
                        @keyup="createAccount.emailEmpty = false"
                        >
                            <template #appendInner>
                                <va-icon
                                name="search"
                                title="Search Account"
                                color="#154EC1"
                                @click="fmode = 0"
                                />
                            </template>
                        </va-input>
                        <va-select
                        class="font-normal"
                        v-if="key.includes('user_type') && fmode"
                        v-model="createAccount.data[key]"
                        :placeholder="acc.createNew[$root.arrayFind(acc.createNew, item => item.key === key)].label + ' *'"
                        :options="key.includes('user_type') && acc.types.filter(item => item.label !== 'Student')"
                        :error="createAccount.userTypeEmpty"
                        @update:modelValue="createAccount.userTypeEmpty = false"
                        :text-by="key.includes('user_type') && 'label'"
                        :value-by="key.includes('user_type') && 'value'"
                        clearable
                        clearable-icon="backspace"
                        />
                        <va-button
                        v-if="key.includes('id') && fmode"
                        preset="primary"
                        icon="person_add"
                        :loading="createAccount.saved"
                        :disabled="!createAccount.saved && (createAccount.data.email == '' && createAccount.data.user_type == '')"
                        @click="createAccount.saved = true, insertUpdateAccount('create')"
                        >
                            Add
                        </va-button>
                    </th>
                </tr>
            </template>

            <template #cell(user_type)="{ value }">
                {{ acc.types[value].label }}
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
                :class="rowData.id === sessionId ? 'opacity-[0!important]' : ''"
                title="Edit"
                preset="plain"
                icon="edit"
                :disabled="rowData.deleted_at || rowData.id === sessionId ? true : false"
                @click="editAccount.data = { ...rowData }, editAccount.modal = !editAccount.modal"
                />
                <va-button
                class="mb-2 mr-2 hover:opacity-[0.65!important]"
                :class="rowData.id === sessionId ? 'opacity-[0!important]' : ''"
                title="Toggle Status"
                preset="plain"
                :icon="rowData.deleted_at ? 'lock' : 'lock_open'"
                :disabled="rowData.deleted_at || rowData.id === sessionId ? true : false"
                @click="editAccount.data = { ...rowData }, editAccount.statusModal = !editAccount.statusModal"
                />
            </template>

            <template #bodyAppend>
                <tr v-if="$root.tblPagination(accounts)">
                    <td
                    id="pagination"
                    :colspan="acc.tblColumns.length"
                    >
                        <div class="flex pt-[10px] select-none">
                            <va-pagination
                            class="justify-center"
                            v-model="$root.config.tblCurrPage"
                            :pages="filter == '' ? $root.tblPagination(accounts) : (pages, $root.config.tblCurrPage = 1)"
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
    v-model="editAccount.modal"
    @cancel="editAccount.data = { ...createAccount.resetData }"
    noOutsideDismiss
    noPadding
    >
        <template #content>
            <div class="w-[410px] p-5">
                <div class="va-title mb-3">
                    Edit Account
                </div>
                <va-input
                v-model="editAccount.data.email"
                type="email"
                label="E-mail Address"
                class="w-full mb-2"
                :rules="[(v) => v && v.length > 0 || 'E-mail address is empty']"
                disabled
                />
                <va-select
                v-model="editAccount.data.user_type"
                class="w-full mb-2"
                label="Type"
                :options="acc.types"
                value-by="value"
                text-by="label"
                />
                <div class="flex w-full gap-x-3 mt-[15px]">
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        preset="secondary"
                        @click="
                            editAccount.data = { ...createAccount.resetData },
                            editAccount.saved = false,
                            editAccount.modal = !editAccount.modal
                        ">
                            <p class="font-normal">Cancel</p>
                        </va-button>
                    </div>
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        icon="save"
                        :loading="editAccount.saved"
                        :disabled="editAccount.saved"
                        @click="editAccount.saved = true, insertUpdateAccount('save')"
                        >
                            <p class="font-normal">Save</p>
                        </va-button>
                    </div>
                </div>
            </div>
        </template>
    </va-modal>

    <va-modal
    v-model="editAccount.statusModal"
    @cancel="editAccount.data = { ...createAccount.resetData }"
    noPadding
    >
        <template #content>
            <div class="w-[410px] p-5">
                <div class="va-title mb-3">
                    Account Status
                </div>
                <va-alert color="warning">
                    <template #icon>
                        <va-icon name="warning" />
                    </template>
                    This action is currently irreversible
                </va-alert>
                <p class="my-7 text-center">
                    <span class="va-text-code">{{ editAccount.data.email }}</span>
                </p>
                <div class="flex w-full gap-x-3 mt-[15px]">
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        preset="secondary"
                        @click="editAccount.data = { ...createAccount.resetData }, editAccount.statusModal = !editAccount.statusModal"
                        >
                            <p class="font-normal">Cancel</p>
                        </va-button>
                    </div>
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        :icon="!editAccount.data.deleted_at ? 'lock' : 'lock_open'"
                        :loading="editAccount.saved"
                        :disabled="editAccount.saved"
                        @click="editAccount.saved = true, toggleAccountStatus()"
                        >
                            <p class="font-normal">{{ !editAccount.data.deleted_at ? "Deactivate" : "Activate" }}</p>
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

const newAccount = {
    email: "",
    user_type: "",
    deleted_at: null,
    created_at: formatDate(new Date().getTime(), 'YYYY-MM-DD HH:mm:ss'),
    id: null,
};

export default {
    data () {
        const acc = {
            tblColumns: [
                { key: "email", label: "E-mail Address", sortable: true },
                { key: "user_type", label: "Type", width: 180, sortable: false },
                { key: "deleted_at", label: "Status", width: 50, sortable: false },
                { key: "created_at", label: "Register Date", width: 125, sortable: true },
                { key: "id", label: "Action", width: 60, sortable: false }
            ],
            createNew: [
                { key: "email", label: "E-mail Address" },
                { key: "user_type", label: "Type" },
                { key: "deleted_at", label: "Status" },
                { key: "created_at", label: "Register Date" },
                { key: "id", label: "Action" },
            ],
            types: [
                { label: "Student", value: 0 },
                { label: "Instructor", value: 1 },
                { label: "Secretary", value: 2 },
                { label: "Administrator", value: 3 }
            ]
        };

        return {
            acc,
            accounts: [],
            account: {},
            createAccount: {
                emailEmpty: false,
                userTypeEmpty: false,
                saved: false,
                resetData: { ...newAccount },
                data: { ...newAccount }
            },
            editAccount: {
                modal: false,
                statusModal: false,
                saved: false,
                data: {}
            },
            filtered: null,
            filter: "",
            fmode: 1
        };
    },
    props: ['sessionId'],
    computed: {
        pages() {
            return this.$root.config.tblPerPage && this.$root.config.tblPerPage !== 0
            ? Math.ceil(this.filtered.length / this.$root.config.tblPerPage)
            : this.filtered.length;
        },
    },
    mounted () {
        this.getAccounts();
    },
    computed: {
        isNewAccount() {
            return Object.keys(this.createAccount.data).every(
                (key) => {
                    const value = this.createAccount.data[key];
                    if (key === 'deleted_at' || key === 'id') {
                        return value === null;
                    }
                    if (key === 'user_type') {
                        return typeof value === 'number';
                    }
                    return !!value;
                }
            );
        }
    },
    methods: {
        toggleAccountStatus() {
            axios({
                method: 'POST',
                type: 'JSON',
                url: '/account/delete',
                data: { id: this.editAccount.data.id }
            }).then(response => {
                if (response.data.status == 1) {
                    this.$root.prompt(response.data.text);
                    this.editAccount.data = { ...newAccount };
                    this.editAccount.saved = false;
                    this.editAccount.statusModal = false;

                    this.getAccounts();
                } else this.$root.prompt(response.data.text);
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
            });
        },
        insertUpdateAccount(method) {
            if (method !== 'create' || method !== 'save') {
                axios({
                    method: 'POST',
                    type: 'JSON',
                    url: '/account/save',
                    data: method === 'create' ? this.createAccount.data : (method === 'save' && this.editAccount.data)
                }).then(response => {
                    if (response.data.status == 1) {
                        this.$root.prompt(response.data.text);
                        method === 'create' ? (
                            this.createAccount.data = { ...newAccount },
                            this.createAccount.saved = false
                        )
                        : (method === 'save' && (
                                this.editAccount.data = { ...newAccount },
                                this.editAccount.modal = false,
                                this.editAccount.saved = false
                            )
                        );

                        this.getAccounts();
                    } else this.$root.prompt(response.data.text);
                }).catch(error => {
                    if (method === 'create') {
                        let resDataError = Object.keys(error.response.data.errors);

                        if (resDataError.filter(key => key == 'email').length) {
                            this.createAccount.emailEmpty = true;
                        }
                        if (resDataError.filter(key => key == 'user_type').length) {
                            this.createAccount.userTypeEmpty = true;
                        }
                        this.$root.prompt(error.response.data.message);
                    }

                    method === 'create' ? this.createAccount.saved = false
                    : (method === 'save' && (this.editAccount.saved = false));
                });
            } else this.$root.prompt();
        },
        getAccounts() {
            axios({
                method: 'GET',
                type: 'JSON',
                url: '/get_accounts'
            }).then(response => {
                if (response.data.status == 1) {
                    this.accounts = response.data.result;
                } else this.$root.prompt(response.data.text);
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
            });
        },
        formatDate
    }
}
</script>
