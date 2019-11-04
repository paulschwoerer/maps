<template>
  <div id="map-container">
    <LMap
      :center="mapOptions.center"
      :maxBounds="mapOptions.maxBounds"
      :minZoom="mapOptions.minZoom"
      :maxZoom="mapOptions.maxZoom"
      :zoom="mapOptions.zoom"
      ref="map"
    >
      <LTileLayer v-for="layer in layers" :key="layer.name" :url="layer.url" />
      <LMarkerCluster
        v-for="categoryKey in Object.keys(favoriteCategories)"
        :key="categoryKey"
        :options="{
          iconCreateFunction: getClusterIconCreateFunction(categoryKey),
          animate: mapOptions.animateClusters,
          showCoverageOnHover: mapOptions.showClusterBounds
        }"
      >
        <LMarker
          v-for="favorite in favoriteCategories[categoryKey]"
          :key="`${favorite.lat}${favorite.lng}`"
          :lat-lng="[favorite.lat, favorite.lng]"
          :icon="createNewDivIcon(categoryKey)"
        ></LMarker>
      </LMarkerCluster>
      <LFeatureGroup ref="popupLayer">
        <LPopup :lat-lng="popup.coordinates">
          <MapClickPopup
            :isVisible="popup.visible"
            :coordinates="popup.coordinates"
            :addFavorite="addFavorite"
            @close="closePopup"
          />
        </LPopup>
      </LFeatureGroup>
    </LMap>
  </div>
</template>

<script>
import L from "leaflet";
import VueTypes from "vue-types";
import "leaflet.markercluster";
import "leaflet.featuregroup.subgroup";

import MapClickPopup from "./map/MapClickPopup";

import { LMap, LTileLayer, LMarker, LPopup, LFeatureGroup } from "vue2-leaflet";
import LMarkerCluster from "vue2-leaflet-markercluster";
import { latLngBounds, latLng } from "leaflet";
import { PUBLIC_FAVORITES_NAMESPACE } from "../store/modules/publicFavorites";
import { mapActions } from "vuex";

const CLUSTER_MARKER_VIEW_SIZE = 27;
const MAP_ID = "map-container";

export default {
  name: "MapContainer",

  props: {
    favoriteCategories: VueTypes.object.isRequired,
    addFavorite: VueTypes.func.isRequired
  },

  created() {
    this.categoryLayers = null;
    this.categoryIcons = null;

    // this.cluster = L.markerClusterGroup({
    //   // iconCreateFunction: this.getClusterIconCreateFunction(), // TODO
    //   spiderfyOnMaxZoom: false,
    //   maxClusterRadius: 28,
    //   zoomToBoundsOnClick: false,
    //   chunkedLoading: true,
    //   icon: {
    //     iconSize: [CLUSTER_MARKER_VIEW_SIZE, CLUSTER_MARKER_VIEW_SIZE]
    //   }
    // });
  },

  mounted() {
    this.$nextTick(() => {
      this.$refs.map.mapObject.on("click", this.handleMapClick);
    });
  },

  data() {
    return {
      popup: {
        visible: false,
        coordinates: [0, 0]
      },
      mapOptions: {
        center: [0, 0],
        zoom: 2,
        minZoom: 2,
        maxZoom: 19,
        bounds: latLngBounds([
          [40.70081290280357, -74.26963806152345],
          [40.82991732677597, -74.08716201782228]
        ]),
        maxBounds: latLngBounds([[-90, 720], [90, -720]]),
        animateClusters: true, // TODO: use setting?
        showClusterBounds: false
      },
      layers: [
        {
          name: "OSM",
          url: "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
        }
      ]
    };
  },

  watch: {
    /*favoriteCategories() {
      if (this.map) {
        const layers = {};
        const icons = {};

        for (const categoryKey of Object.keys(this.favoriteCategories)) {
          icons[categoryKey] = L.divIcon({
            iconAnchor: [9, 9],
            className: "leaflet-marker-favorite",
            html:
              '<div class="favoriteMarker ' +
              categoryKey +
              'CategoryMarker"></div>'
          });

          const markers = this.favoriteCategories[categoryKey].map(favorite => {
            return L.marker(L.latLng(favorite.lat, favorite.lng), {
              icon: icons[categoryKey]
            });
          });

          layers[categoryKey] = L.featureGroup.subGroup(this.cluster, markers);
          layers[categoryKey].addTo(this.map);
        }

        this.cluster.addTo(this.map);

        this.categoryLayers = layers;
        this.categoryIcons = icons;
      }
    }*/
  },

  methods: {
    handleMapClick(e) {
      if (this.popup.visible) {
        this.closePopup();
      } else {
        this.openPopup(e.latlng.lat, e.latlng.lng);
      }
    },

    createNewDivIcon(categoryKey) {
      return new L.DivIcon({
        iconAnchor: [9, 9],
        className: "leaflet-marker-favorite",
        html:
          '<div class="favorite-marker ' +
          categoryKey +
          'CategoryMarker"></div>'
      });
    },

    getClusterIconCreateFunction(categoryKey) {
      return cluster => {
        const label = cluster.getChildCount();

        return new L.DivIcon({
          iconAnchor: [14, 14],
          className: "leaflet-marker-favorite-cluster cluster-marker",
          html:
            '<div class="favorite-cluster-marker ' +
            categoryKey +
            'CategoryMarker"></div>​<span class="label">' +
            label +
            "</span>"
        });
      };
    },

    openPopup(lat, lng) {
      this.$refs.popupLayer.mapObject.openPopup([lat, lng]);
      this.popup.visible = true;
      this.popup.coordinates = [lat, lng];
    },

    closePopup() {
      this.$refs.popupLayer.mapObject.closePopup();
      this.popup.visible = false;
      this.popup.coordinates = [0, 0];
    },

    initMap() {
      const starImageUrl = OC.generateUrl(
        "/svg/core/actions/star-dark?color=000000"
      );
      const markerRedImageUrl = OC.generateUrl(
        "/svg/core/actions/address?color=EE3333"
      );
      const markerBlueImageUrl = OC.generateUrl(
        "/svg/core/actions/address?color=3333EE"
      );
      const markerGreenImageUrl = OC.generateUrl(
        "/svg/core/actions/address?color=33EE33"
      );
      const photoImageUrl = OC.generateUrl(
        "/svg/core/places/picture?color=000000"
      );
      const contactImageUrl = OC.generateUrl(
        "/svg/core/actions/user?color=000000"
      );
      const shareImageUrl = OC.generateUrl(
        "/svg/core/actions/share?color=000000"
      );

      this.map = L.map(MAP_ID, {
        zoom: 2,
        zoomControl: false,
        maxZoom: 19,
        minZoom: 2,
        center: new L.LatLng(0, 0),
        closePopupOnClick: true,
        maxBounds: new L.LatLngBounds(
          new L.LatLng(-90, 720),
          new L.LatLng(90, -720)
        ),
        layers: [],
        // right click menu
        contextmenu: false,
        contextmenuWidth: 160,
        contextmenuItems: [
          {
            text: t("maps", "Add a favorite"),
            icon: starImageUrl,
            callback: this.$emit("addFavorite")
          },
          /*{
            text: t("maps", "Place photos"),
            icon: photoImageUrl,
            callback: this.$emit("placePhotos")
            //}, {
            //    text: t('maps', 'Place photo folder'),
            //    icon: photoImageUrl,
            //    callback: photosController.contextPlacePhotoFolder
          },
          {
            text: t("maps", "Place contact"),
            icon: contactImageUrl,
            callback: this.$emit("placeContact")
          },
          {
            text: t("maps", "Share this location"),
            icon: shareImageUrl,
            callback: this.$emit("shareLocation")
          },*/
          "-"
          /*{
            text: t("maps", "Route from here"),
            icon: markerGreenImageUrl,
            callback: this.$emit("routeFrom")
          },
          {
            text: t("maps", "Add route point"),
            icon: markerBlueImageUrl,
            callback: this.$emit("routePoint")
          },
          {
            text: t("maps", "Route to here"),
            icon: markerRedImageUrl,
            callback: this.$emit("routeTo")
          }*/
        ]
      });

      /*this.map.on("contextmenu", e => {
        if ($(e.originalEvent.target).attr("id") === MAP_ID) {
          this.openPopup(e.latlng.lat, e.latlng.lng);
        }
      });*/

      this.map.on("click", e => {
        if ($(e.originalEvent.target).attr("id") === MAP_ID) {
          this.openPopup(e.latlng.lat, e.latlng.lng);
        }
      });

      const osm = L.tileLayer(
        "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
        {
          attribution:
            '&copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>',
          noWrap: false,
          detectRetina: false,
          maxZoom: 19
        }
      );

      const roadsOverlay = L.tileLayer(
        "https://{s}.tile.openstreetmap.se/hydda/roads_and_labels/{z}/{x}/{y}.png",
        {
          maxZoom: 18,
          opacity: 0.7,
          attribution:
            '<a href="http://openstreetmap.se/" target="_blank">OpenStreetMap Sweden</a> &mdash; Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }
      );

      // tile layer selector
      const baseLayers = {
        OpenStreetMap: osm
        // "ESRI Aerial": ESRIAerial,
        // "ESRI Topo": ESRITopo,
        // OpenTopoMap: openTopo,
        // Dark: dark,
        // Watercolor: watercolor
      };
      const baseOverlays = {
        "Roads and labels": roadsOverlay
      };
      const controlLayers = L.control
        .layers(baseLayers, baseOverlays, {
          position: "bottomright",
          collapsed: false
        })
        .addTo(this.map);

      const locale = OC.getLocale();
      const imperial =
        locale === "en_US" ||
        locale === "en_GB" ||
        locale === "en_AU" ||
        locale === "en_IE" ||
        locale === "en_NZ" ||
        locale === "en_CA";
      const metric = !imperial;

      L.control
        .scale({ metric: metric, imperial: imperial, position: "bottomleft" })
        .addTo(this.map);

      L.control.zoom({ position: "bottomright" }).addTo(this.map);
    }
  },

  components: {
    MapClickPopup,
    LMap,
    LFeatureGroup,
    LMarker,
    LMarkerCluster,
    LTileLayer,
    LPopup
  }
};
</script>

<style lang="scss">
#map-container {
  position: relative;
  height: 100%;
  width: 100%;

  * {
    box-sizing: content-box;
  }

  .leaflet-tooltip {
    white-space: normal !important;
  }

  .leaflet-container {
    background: var(--color-main-background);
  }

  .leaflet-control-layers-base {
    line-height: 30px;
  }

  .leaflet-control-layers-selector {
    min-height: 0;
  }

  .leaflet-control-layers-toggle {
    background-size: 75% !important;
  }

  .leaflet-control-layers:not(.leaflet-control-layers-expanded) {
    width: 33px;
    height: 37px;
  }

  .leaflet-control-layers:not(.leaflet-control-layers-expanded) > a {
    width: 100%;
    height: 100%;
  }

  .favorite-marker,
  .favorite-cluster-marker {
    /*-webkit-mask: url("../../css/images/star-circle.svg") no-repeat 50% 50%;
    mask: url("../../css/images/star-circle.svg") no-repeat 50% 50%;
    background: url("../../css/images/star-white.svg") no-repeat 50% 50%; */ // TODO: webpack image/svg config
    background: red; // TODO: remove
    border-radius: 50%;
    box-shadow: 0 0 10px #888;
  }

  .favorite-marker {
    height: 18px !important;
    width: 18px !important;
    /*-webkit-mask-size: 18px;
    mask-size: 18px;
    background-size: 18px 18px;*/
  }

  .favorite-cluster-marker {
    height: 27px !important;
    width: 27px !important;
    /*-webkit-mask-size: 27px;
    mask-size: 27px;
    background-size: 27px 27px;*/
  }

  .leaflet-marker-favorite-cluster {
    .label {
      position: absolute;
      top: -7px;
      right: 0;
      color: #fff;
      background-color: #333;
      border-radius: 9px;
      height: 18px;
      min-width: 18px;
      line-height: 12px;
      text-align: center;
      padding: 3px;
    }
  }

  /* Adjust button styles to Nextcloud */
  .leaflet-touch {
    .leaflet-control-layers,
    .leaflet-bar {
      border: none;
      border-radius: var(--border-radius);
    }
  }

  /* Fix attribution overlapping map on mobile */
  .leaflet-control-attribution.leaflet-control {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 50vw;
  }
}
</style>
