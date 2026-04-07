<template>
    <div id="app">
        <NotificationCenter />
        <ConfirmDialog />
        <router-view />
    </div>
</template>

<script>
import NotificationCenter from "./NotificationCenter.vue";
import ConfirmDialog from "./ConfirmDialog.vue";

export default {
    name: "App",
    components: {
        NotificationCenter,
        ConfirmDialog,
    },
    mounted() {
        // Initialize app by checking auth status from session
        this.checkAuthStatus();
    },
    methods: {
        async checkAuthStatus() {
            try {
                // Try to determine if user is logged in by checking session
                // This is handled by Laravel session middleware
            } catch (error) {
                console.error("Error checking auth status:", error);
            }
        },
    },
};
</script>

<style scoped>
#app {
    width: 100%;
    height: 100%;
}
</style>
