<template>
    <div class="p-4" :class="{'mb-16': !!filteredCountry}">
        <header>
            <h3>Covid Stats</h3>
        </header>

        <button v-if="filteredCountry"
                class="button border border-gray-200 fixed right-0 mr-4 bottom-0 mb-6 rounded-full"
                @click.prevent="filterCountry(null)">
            {{ filteredCountry.country.name }} <i class="fas fa-times"></i>
        </button>

        <div class="grid grid-cols-2 gap-4 md:flex md:justify-between">
            <stats-tile title="Confirmed" :cases="filteredStats.total_confirmed" color="orange"/>
            <stats-tile title="Active" :cases="filteredStats.new_confirmed" color="blue"/>
            <stats-tile title="Deaths" :cases="filteredStats.total_deaths" color="red"/>
            <stats-tile title="Recovered" :cases="filteredStats.total_recovered" color="green"/>
        </div>

        <div class="mt-4 md:grid md:grid-cols-2 md:gap-4">
            <div>
                <h4>Top 10 countries by cases</h4>
                <hr class="border-2 border-black"/>
                <div class="mt-2 max-h-screen overflow-auto">
                    <a class="flex justify-between py-1" :class="{ 'border-t-2': i !== 0 }"
                       v-for="(item, i) in filteredCountries"
                       :key="i"
                       @click.prevent="filterCountry(i)"
                    >
                        <div class="font-semibold">{{ item.country.name }} ({{ item.country.code }})</div>
                        <div>{{ item.stats.total_confirmed.toString().toSeparatedNumber() }}</div>
                    </a>
                </div>

                <button v-if="countries.length" class="button w-full mt-4 font-semibold" @click.prevent="toggleShowAllCountries()">
                    {{ showAllCountries ? 'Hide' : 'Show' }} all countries
                </button>

                <div class="text-sm bg-orange-100 p-2 shadow-md rounded-md flex mt-2">
                    <div class="font-semibold"><i class="far fa-lightbulb mr-1"></i>Tip:</div>
                    <div class="ml-2">Selecting a country filters the tiles.</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import statsQuery from '../queries/stats.gql';
import countriesQuery from '../queries/countriesList.gql';

import StatsTile from "../components/StatsTile";

export default {
    components: {
        StatsTile
    },

    apollo: {
        stats: {
            query: statsQuery
        },

        countries: {
            query: countriesQuery,

            update: data => data.countriesList
        },
    },

    data() {
        return {
            stats: {
                new_confirmed: 0,
                total_confirmed: 0,
                new_deaths: 0,
                total_deaths: 0,
                new_recovered: 0,
                total_recovered: 0,
            },

            countries: [],

            country: null,

            showAllCountries: false
        }
    },

    computed: {
        filteredCountries() {
            if (this.showAllCountries) {
                return this.countries;
            }

            return this.countries.slice(0, 10);
        },

        filteredStats() {
            if (this.country == null) {
                return this.stats;
            }

            if (!this.filteredCountry) {
                return this.stats;
            }

            return this.filteredCountry.stats;
        },

        filteredCountry() {
            return this.countries[this.country] || null;
        }
    },

    methods: {
        filterCountry(index) {
            this.country = index;
        },

        toggleShowAllCountries(value) {
            this.showAllCountries = value != null ? value : !this.showAllCountries;
        },
    }
}
</script>
