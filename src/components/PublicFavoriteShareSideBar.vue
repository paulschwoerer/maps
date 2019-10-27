<template>
  <AppNavigation>
    <ul id="app-vueexample-navigation">
      <AppNavigationCaption title="Lala" />
      <AppNavigationItem
        v-for="item in favorites"
        :key="item.name"
        :title="item.name"
      />
    </ul>
    <AppNavigationSettings>
      Example settings
    </AppNavigationSettings>
  </AppNavigation>
</template>

<script>
import AppNavigation from "@nextcloud/vue/dist/Components/AppNavigation";
import AppNavigationCaption from "@nextcloud/vue/dist/Components/AppNavigationCaption";
import AppNavigationSettings from "@nextcloud/vue/dist/Components/AppNavigationSettings";
import AppNavigationItem from "@nextcloud/vue/dist/Components/AppNavigationItem";
import {
  apiRequest,
  getCurrentPublicShareToken,
  showNotification
} from "../utils/common";

export default {
  name: "SideBar",

  mounted() {
    console.log(getCurrentPublicShareToken());
    apiRequest(
      `/apps/maps/api/1.0/public/${getCurrentPublicShareToken()}/favorites`,
      "GET"
    )
      .then(data => {
        this.favorites = data;
      })
      .catch(() =>
        showNotification(t("maps", "Failed to share favorites category"))
      );
  },

  data() {
    return {
      favorites: []
    };
  },

  components: {
    AppNavigation,
    AppNavigationCaption,
    AppNavigationSettings,
    AppNavigationItem
  }
};
</script>

<style scoped></style>
