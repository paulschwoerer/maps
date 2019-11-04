<template>
  <div id="maps-app">
    <PublicFavoriteShareSideBar />
    <div class="content-wrapper">
      <MapContainer
        @mapLeftClick="handleMapLeftClick"
        :favorite-categories="favoritesMappedByCategory"
        :propagate-left-click="mapPropagateLeftClick"
        :addFavorite="addFavorite"
      />
    </div>
  </div>
</template>

<script>
import MapContainer from "./components/MapContainer";
import PublicFavoriteShareSideBar from "./components/PublicFavoriteShareSideBar";
import {
  request,
  getCurrentPublicShareToken,
  showNotification,
  publicApiRequest
} from "./utils/common";
import { getCategoryRawName } from "./utils/mapUtils";
import { mapActions, mapGetters, mapState } from "vuex";
import { PUBLIC_FAVORITES_NAMESPACE } from "./store/modules/publicFavorites";
import AppMode from "./data/enum/MapMode";

export default {
  name: "App",

  data() {
    return {
      mode: "default"
    };
  },

  mounted() {
    this.getFavorites();
  },

  computed: {
    ...mapState({
      appMode: state => state[PUBLIC_FAVORITES_NAMESPACE].appMode
    }),
    ...mapGetters({
      favoritesMappedByCategory: `${PUBLIC_FAVORITES_NAMESPACE}/mappedByCategory`
    }),
    mapPropagateLeftClick() {
      return this.appMode === AppMode.ADDING_FAVORITES;
    }
  },

  methods: {
    ...mapActions({
      getFavorites: `${PUBLIC_FAVORITES_NAMESPACE}/getFavorites`,
      addFavorite: `${PUBLIC_FAVORITES_NAMESPACE}/addFavorite`
    }),

    handleMapLeftClick(e) {
      if (this.appMode === AppMode.ADDING_FAVORITES) {
      }
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
