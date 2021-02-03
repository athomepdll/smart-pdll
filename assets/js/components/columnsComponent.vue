<template>
    <div>

        <table :config="config" class="table">
            <thead>
            <tr>
                <th>Nom de la colonne dans le fichier</th>
                <th>Niveau</th>
                <th>Format</th>
                <th>Champ de destination</th>
                <th>
                    <button v-if="isEditable === 1" type="button" @click="addItem" class="btn btn-primary" >Ajouter</button>
                </th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(item, index) in items">
                <td>
                    <input class="form-control" v-model="item.columnName" type="text" @change="patchItem(item)" :disabled="isEditable === 0"/>
                </td>
                <td>
                    <select class="form-control" v-model="item.dataLevel" @change="patchItem(item)" :disabled="isEditable === 0">
                        <option v-for="datum_level in data_level" v-bind:value="datum_level.id">
                            {{datum_level.value}}
                        </option>
                    </select>
                </td>
                <td>
                    <select class="form-control" v-model="item.dataType" @change="patchItem(item)" :disabled="isEditable === 0">
                        <option v-for="datum_type in data_type" v-bind:value="datum_type.id">
                            {{datum_type.value}}
                        </option>
                    </select>
                </td>
                <td>
                    <select class="form-control" v-model="item.identifier" @change="patchItem(item)" :disabled="isEditable === 0">
                        <option value=""></option>
                        <option v-for="datum_field in financial_fields" v-bind:value="datum_field.value">
                            {{datum_field.value}}
                        </option>
                    </select>
                </td>
                <td>
                    <button v-if="isEditable === 1" type="button" @click="removeItem(index)" class="btn btn-danger">Supprimer</button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        name: "columnsComponent",
        data() {
            return {
                api: {
                    'get': '/api/datacolumns',
                    'enumeration': '/api/enumerations',
                },
                items: [],
                data_level: [],
                data_type: [],
                financial_fields: [],
            };
        },
        mounted() {
            console.log(this.isEditable);
            this.getColumns();
            this.getDataLevel();
            this.getDataType();
            this.getFinancialFields();
        },
        methods: {
            addItem: async function () {
                const newItem = {
                    identifier: "",
                    attribute: "",
                    config: this.config,
                    columnName: "",
                    dataLevel: "",
                    dataType: "",
                    id: "",
                };
                try {
                    let response = await axios.post(process.env.API_HOST + this.api.get, newItem);
                    newItem.id = response.data.id;
                    this.items.push(newItem);
                } catch (error) {
                    throw error;
                }
            },
            patchItem: async function (item) {
                try {
                    let response = await axios.patch(process.env.API_HOST + this.api.get + '/' + item.id, item);
                    item.id = response.data.newColumnId;
                } catch (error) {
                    throw error;
                }
            }
            ,
            removeItem: async function (item) {
                try {
                    let response = await axios.delete(process.env.API_HOST + this.api.get + '/' + this.items[item].id);
                    this.items.splice(item, 1);
                } catch (error) {
                    throw error;
                }
            },
            getColumns: async function () {
                try {
                    let response = await axios.get(process.env.API_HOST + this.api.get + '?config=' + this.config);
                    this.items = response.data.columns;
                } catch (error) {
                    throw error;
                }
            },
            getDataLevel: async function () {
                try {
                    let response = await axios.get(process.env.API_HOST + this.api.enumeration + '?discr=data_level');
                    this.data_level = response.data.enumerations;
                } catch (error) {
                    throw error;
                }
            },
            getDataType: async function () {
                try {
                    let response = await axios.get(process.env.API_HOST + this.api.enumeration + '?discr=data_type');
                    this.data_type = response.data.enumerations;
                } catch (error) {
                    throw error;
                }
            },
            getFinancialFields: async function () {
                try  {
                    let response = await axios.get(process.env.API_HOST + this.api.enumeration + '?discr=financial_field');
                    this.financial_fields = response.data.enumerations;
                } catch (error) {
                    throw error;
                }
            }
        },
        props: {
            config: {
                type: Number
            },
            isEditable: {
                type: Number,
                default: 1
            }
        }
    }
</script>

<style scoped>

</style>