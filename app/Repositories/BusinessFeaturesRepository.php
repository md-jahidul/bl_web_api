<?php

/**
 * User: Bulbul Mahmud Nito
 * Date: 11/02/2020
 */

namespace App\Repositories;

use App\Models\BusinessFeatures;

class BusinessFeaturesRepository extends BaseRepository {

    public $modelName = BusinessFeatures::class;

    public function getFeaturesList() {
        $data = $this->model->orderBy('sort')->get();
        return $data;
    }
    public function getActiveFeaturesList() {
        $data = $this->model->where('status', 1)->orderBy('sort')->get();
        return $data;
    }

    public function changeFeatureSorting($request) {
        try {

            $positions = $request->position;
            foreach ($positions as $position) {
                $featureId = $position[0];
                $new_position = $position[1];
                $update = $this->model->findOrFail($featureId);
                $update['sort'] = $new_position;
                $update->update();
            }
            
            $response = [
                'success' => 1,
                'message' => 'Success'
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response = [
                'success' => 0,
                'message' => $e->getMessage()
            ];
            return response()->json($response, 500);
        }
    }

    public function changeFeatureStatus($featureId) {
        try {

            $feature = $this->model->findOrFail($featureId);

            $status = $feature->status == 1 ? 0 : 1;
            $feature->status = $status;
            $feature->save();

            $response = [
                'success' => 1,
                'status' => $status,
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response = [
                'success' => 0,
                'errors' => $e->getMessage()
            ];
            return response()->json($response, 500);
        }
    }

    public function saveFeature($filePath, $request) {

        $featureId = $request->feature_id;
        if ($featureId != "") {
            $feature = $this->model->findOrFail($featureId);
        } else {
            $feature = $this->model;
            $feature->status = 1;
            $feature->sort = 0;
        }
        if ($filePath != "") {
            $feature->icon_url = $filePath;
        }
        $feature->title = $request->title;
        $feature->title_bn = $request->title_bn;
        $feature->alt_text = $request->alt_text;
        return $feature->save();
    }

    public function getSingleFeature($featureId) {
        $data = $this->model->findOrFail($featureId);
        return $data;
    }

    public function deleteFeature($featureId) {
        try {

            $feature = $this->model->findOrFail($featureId);
            $photo = $feature->icon_url;
            $feature->delete();

            $response = [
                'success' => 1,
                'photo' => $photo,
            ];
            return $response;
        } catch (\Exception $e) {
            $response = [
                'success' => 0,
                'photo' => "",
                'errors' => $e->getMessage()
            ];
            return $response;
        }
    }

}
