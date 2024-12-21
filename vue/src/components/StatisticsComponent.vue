<script setup>
import { ref, onMounted, computed } from 'vue';
import { useStatisticsStore } from '@/stores/statistics';
import { Line, Pie, Bar } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, LineElement, ArcElement, PointElement, CategoryScale, LinearScale, BarElement } from 'chart.js';
import axios from 'axios';
import { useAuthStore } from '../stores/auth';

ChartJS.register(Title, Tooltip, Legend, LineElement, ArcElement, PointElement, CategoryScale, LinearScale, BarElement);

const statisticsStore = useStatisticsStore()
const storeAuth = useAuthStore()
const isAdmin = computed(() => storeAuth.userType === 'A')

const chartData = ref({
        labels: [],
        datasets: [{
                borderWidth: 1,
                data: [],
                segment: {
                        borderColor: (ctx) => {
                                const { p0, p1 } = ctx;
                                const startValue = p0.raw;
                                const endValue = p1.raw;
                                return endValue > startValue ? 'rgba(0,170, 0, 1)' : 'rgba(255, 99, 132, 1)';
                        }
                }
        }],
});

const pieChartData = ref({
        labels: ['Singleplayer', 'Multiplayer'],
        datasets: [
                {
                        backgroundColor: ['#4CAF50', '#FF5722'], 
                        data: [0, 0], 
                },
        ],
});

const selectedProfitTimePeriod = ref('this_year');
const totalProfit = ref(0);
const averageProfit = ref(0);
const medianProfit = ref(0);

const timePeriodOptions = [
        { label: 'Today', value: 'today' },
        { label: 'This Week', value: 'this_week' },
        { label: 'This Month', value: 'this_month' },
        { label: 'This Year', value: 'this_year' }
];

const calculateMedian = (values) => {
        if (values.length === 0) return 0;
        const sorted = [...values].sort((a, b) => a - b);
        const mid = Math.floor(sorted.length / 2);
        return sorted.length % 2 !== 0 
                ? sorted[mid] 
                : (sorted[mid - 1] + sorted[mid]) / 2;
};

const updateGraph = async () => {
        try {
                const fetchedData = await statisticsStore.getDetailedProfit(selectedProfitTimePeriod.value);

                const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

                const labels = fetchedData.map(item => monthNames[item.month - 1] || `Day ${item.day}`);
                const values = fetchedData.map(item => Number(item.total));

                chartData.value = {
                        labels: labels,
                        datasets: [{
                                borderWidth: 1,
                                data: values,
                                segment: {
                                        borderColor: (ctx) => {
                                                const { p0, p1 } = ctx;
                                                const startValue = p0.raw;
                                                const endValue = p1.raw;
                                                return endValue > startValue ? 'rgba(0,170, 0, 1)' : 'rgba(255, 99, 132, 1)';
                                        }
                                }
                        }]
                };

                totalProfit.value = values.reduce((sum, value) => sum + value, 0);
                averageProfit.value = values.length > 0 ? (totalProfit.value / values.length).toFixed(2) : 0;
                medianProfit.value = calculateMedian(values);

        } catch (error) {
                console.error('Error fetching or processing data:', error);
        }
};


const selectedUsersTimePeriod = ref('this_year');
const totalUsers = ref(0);
const topUsers = ref();

const topUsersData = ref({
        labels: ['User 1', 'User 2', 'User 3', 'User 4', 'User 5'], 
        datasets: [
                {
                        label: 'Total Spent (€)',
                        data: [500, 400, 300, 200, 100], 
                        backgroundColor: ['#FFD700', '#FFC300', '#FFB000', '#FFA500', '#FF8C00'], 
                        borderWidth: 1,
                        borderRadius: 5,
                },
        ],
});

const getUsers = async () => {
        try {
                const fetchedUsersData = await statisticsStore.getTotalUsers(selectedUsersTimePeriod.value);
                totalUsers.value = fetchedUsersData.total_users || 0;
                const users = fetchedUsersData.top_spenders; 
                const nicknames = users.map(user => user.nickname);
                const data = users.map(user => user.total_spent);

                topUsersData.value = {
                        labels: nicknames,
                        datasets: [
                                {
                                        label: 'Total Spent (€)',
                                        data: data,
                                        backgroundColor: ['#FFD700', '#FFC300', '#FFB000', '#FFA500', '#FF8C00'],
                                        borderWidth: 1,
                                        borderRadius: 5,
                                },
                        ],
                };

        } catch (error) {
                console.error('Error fetching or processing user data:', error);
        }
};



const selectedGamesTimePeriod = ref('this_year');
const totalGames = ref(0);
const singlePlayer = ref(0);
const multiPlayer = ref(0);

const getTotalGames = async (timeRange) => {
        let params = new URLSearchParams();
        params.append('time_range', timeRange);

        try {
                const response = await axios.get('/statistics/total-games', { params });
                totalGames.value = response.data.total_games || 0; 
                singlePlayer.value = response.data.games_by_type.singleplayer || 0;
                multiPlayer.value = response.data.games_by_type.multiplayer || 0;



                pieChartData.value = {
                        labels: ['Singleplayer', 'Multiplayer'],
                        datasets: [
                                {
                                        backgroundColor: ['#4CAF50', '#FF5722'], 
                                        data: [singlePlayer.value, multiPlayer.value], 
                                },
                        ],
                };


        } catch (e) {
                console.error('Error fetching games data:', e);
        }
};


onMounted(() => {
        if(isAdmin.value)
                updateGraph();
        getUsers();
        getTotalGames('this_year'); 
});


const handleProfitTimePeriodChange = () => {
        updateGraph();
};

const handleUsersTimePeriodChange = () => {
        getUsers();
};

const handleGamesTimePeriodChange = () => {
        getTotalGames(selectedGamesTimePeriod.value);
};
</script>

<template>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-4 h-screen">
                <!-- Profit Card -->
                <div v-if="isAdmin" class="col-span-1 md:col-span-1 bg-white p-4 rounded-lg shadow-md border-2 border-green-600 flex flex-col h-full">
                        <h2 class="text-2xl font-bold mb-4 text-green-600">Profit</h2>

                        <!-- Time Period Selector -->
                        <div class="mb-4">
                                <label for="profitTimePeriod" class="mr-2 text-green-600">Select Time Period:</label>
                                <select 
                                        id="profitTimePeriod" 
                                        v-model="selectedProfitTimePeriod" 
                                        @change="handleProfitTimePeriodChange"
                                        class="p-2 border rounded text-green-600"
                                >
                                        <option 
                                                v-for="option in timePeriodOptions" 
                                                :key="option.value" 
                                                :value="option.value"
                                        >
                                                {{ option.label }}
                                        </option>
                                </select>
                        </div>

                        <!-- Profit Stats -->
                        <div class="mb-4 text-lg text-green-600">
                                <p><strong>Total Profit:</strong> €{{ totalProfit }}</p>
                                <p><strong>Average Profit:</strong> €{{ averageProfit }}</p>
                                <p><strong>Median Profit:</strong> €{{ medianProfit }}</p>
                        </div>

                        <!-- Chart Section (take remaining space) -->
                        <div class="flex-grow w-full">
                                <div v-if="chartData.labels.length === 0 || totalProfit === 0">
                                        <p class="text-center text-xl font-semibold text-gray-500">No money was grabbed</p>
                                </div>
                                <div v-else>
                                        <Line :data="chartData" :options="{
                                                responsive: true,
                                                plugins: {
                                                        legend:{display:false},
                                                },
                                                scales: {
                                                        y: { min: 0 }
                                                }
                                        }" />
                                </div>
                        </div>
                </div>

                <!-- Right Side: Total Users and Total Games Cards -->
                <div class="col-span-1 md:col-span-1 flex flex-col gap-8 h-fit">
                        <!-- Total Users Card -->
                        <div class="bg-white p-4 rounded-lg shadow-md border-2 border-blue-600 flex-1 w-full">
                                <h2 class="text-2xl font-bold mb-4 text-blue-600">Total Users</h2>
                                <div class="mb-4">
                                        <label for="usersTimePeriod" class="mr-2 text-blue-600">Select Time Period:</label>
                                        <select 
                                                id="usersTimePeriod" 
                                                v-model="selectedUsersTimePeriod" 
                                                @change="handleUsersTimePeriodChange"
                                                class="p-2 border rounded text-blue-600"
                                        >
                                                <option 
                                                        v-for="option in timePeriodOptions" 
                                                        :key="option.value" 
                                                        :value="option.value"
                                                >
                                                        {{ option.label }}
                                                </option>
                                        </select>
                                </div>

                                <div class="text-lg text-blue-600 w-fit">
                                        <p class="font-semibold">Total Users: {{ totalUsers }}</p>
                                </div>

                                <!-- Top 5 Losers Chart inside Total Users Card -->
                                <div v-if="isAdmin" class="flex flex-col items-center w-full mt-6">
                                        <!-- Title centered above the chart -->
                                        <h2 class="text-2xl font-bold mb-4 text-blue-600 text-center">Top 5 Supporters</h2>

                                        <!-- Bar Chart (Centered) -->
                                        <div v-if="!topUsersData.datasets[0].data[0]">
                                                <p class="text-center text-xl font-semibold text-gray-500">No supporters to show</p>
                                        </div>
                                        <div v-else class="flex justify-center items-center h-80 w-full">
                                                <Bar 
                                                :data="topUsersData" 
                                                :options="{
                                                        responsive: true,
                                                        indexAxis: 'y',
                                                        maintainAspectRatio: true,
                                                        plugins: {
                                                                legend: { display: false },
                                                                tooltip: {
                                                                        callbacks: {
                                                                                label: function(context) {
                                                                                        const userIndex = context.dataIndex;
                                                                                        const fullName = topUsersData.labels[userIndex]; 
                                                                                        return `${fullName}: ${context.raw}€`;  
                                                                                }
                                                                        }
                                                                }
                                                        },
                                                        scales: {
                                                                x: {
                                                                        beginAtZero: true,
                                                                        ticks: {
                                                                                callback: function(value) {
                                                                                        return `${value}€`; 
                                                                                }
                                                                        }
                                                                },
                                                                y: {
                                                                        ticks: {
                                                                                callback: function(value, index) {
                                                                                        return topUsersData.labels[index];  
                                                                                }
                                                                        }
                                                                }
                                                        }
                                                }" 
                                        />
                                        </div>
                                </div>
                        </div>

                        <!-- Total Games Card -->
                        <div class="bg-white p-4 rounded-lg shadow-md border-2 border-red-600 flex-1 h-fit w-full">
                                <h2 class="text-2xl font-bold mb-4 text-red-600">Total Games</h2>
                                <div class="mb-4">
                                        <label for="gamesTimePeriod" class="mr-2 text-red-600">Select Time Period:</label>
                                        <select 
                                                id="gamesTimePeriod" 
                                                v-model="selectedGamesTimePeriod" 
                                                @change="handleGamesTimePeriodChange"
                                                class="p-2 border rounded text-red-600"
                                        >
                                                <option 
                                                        v-for="option in timePeriodOptions" 
                                                        :key="option.value" 
                                                        :value="option.value"
                                                >
                                                        {{ option.label }}
                                                </option>
                                        </select>
                                </div>                               
                                <!-- Use grid or flexbox to position content side by side -->
                                <div class="flex items-start">
                                        <!-- Total Games Info -->
                                        <div v-if="totalGames === 0" class="text-center text-xl font-semibold text-gray-500 w-full">
                                                <p>No games played</p>
                                        </div>
                                        <div v-else class="text-lg text-red-600 w-48">
                                                <p class="font-semibold">Total Games: {{ totalGames }}</p>
                                                <p class="font-semibold">Singleplayer: {{ singlePlayer }}</p>
                                                <p class="font-semibold">Multiplayer: {{ multiPlayer }}</p>
                                        </div>

                                        <!-- Pie Chart -->
                                        <div v-if="totalGames > 0" class="ml-8 w-max ">
                                                <Pie 
                                                :data="pieChartData" 
                                                :options="{
                                                        responsive: true,
                                                        maintainAspectRatio: true,
                                                        plugins: {
                                                                legend: { display: true },
                                                                title: { 
                                                                        display: true, 
                                                                        text: 'Singleplayer vs Multiplayer',
                                                                        font: { size: 20 }
                                                                },
                                                                tooltip: {
                                                                        callbacks: {
                                                                                label: function(context) {
                                                                                        const total = context.dataset.data.reduce((sum, val) => sum + val, 0);
                                                                                        const value = context.raw;
                                                                                        const percentage = ((value / total) * 100).toFixed(1);
                                                                                        return `${context.label}: ${value} (${percentage}%)`;
                                                                                }
                                                                        }
                                                                }
                                                        }
                                                }" 
                                        />
                                        </div>
                                </div>
                        </div>
                </div>
        </div>
</template>
