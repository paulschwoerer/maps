<template>
  <AppNavigation>
    <ul id="app-vueexample-navigation">
      <AppNavigationNew :text="t('maps', 'Add favorite')" />
      <AppNavigationItem
        v-for="favorite in favorites"
        :key="favorite.name"
        :title="favorite.name"
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
import AppNavigationNew from "@nextcloud/vue/dist/Components/AppNavigationNew";
import VueTypes from "vue-types";

export default {
  name: "SideBar",

  props: {
    favorites: VueTypes.arrayOf(VueTypes.object).isRequired
  },

  components: {
    AppNavigation,
    AppNavigationCaption,
    AppNavigationSettings,
    AppNavigationItem,
    AppNavigationNew
  }
};
</script>

<style scoped></style>
