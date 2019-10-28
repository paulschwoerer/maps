<template>
  <div id="maps-app">
    <PublicFavoriteShareSideBar :favorites="store.favorites" />
    <div class="content-wrapper">
      <MapContainer
        @mapLeftClick="handleMapLeftClick"
        :favorite-categories="favoriteCategories"
      />
    </div>
  </div>
</template>

<script>
import MapContainer from "./components/MapContainer";
import PublicFavoriteShareSideBar from "./components/PublicFavoriteShareSideBar";
import {
  apiRequest,
  getCurrentPublicShareToken,
  showNotification
} from "./utils/common";
import { getCategoryRawName } from "./utils/mapUtils";

export default {
  name: "App",

  data() {
    return {
      // TODO: use vuex
      store: {
        favorites: []
      }
    };
  },

  mounted() {
    apiRequest(
      `/apps/maps/api/1.0/public/${getCurrentPublicShareToken()}/favorites`,
      "GET"
    )
      .then(data => {
        this.store.favorites = data;
      })
      .catch(() =>
        showNotification(t("maps", "Failed to share favorites category"))
      );
  },

  computed: {
    favoriteCategories() {
      const favorites = this.store.favorites;

      if (favorites.length === 0) {
        return {};
      }

      return {
        [getCategoryRawName(favorites[0].category)]: favorites
      };
    }
  },

  methods: {
    handleMapLeftClick(e) {
      console.log(e);
    }
  },

  components: {
    MapContainer,
    PublicFavoriteShareSideBar
  }
};
</script>

<style lang="scss">
#maps-app {
  width: 100%;

  .content-wrapper {
    margin-left: 300px;
    height: 100%;
  }
}
</style>
