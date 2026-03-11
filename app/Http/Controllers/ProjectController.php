<?php

namespace App\Http\Controllers;

use App\Enums\HttpCode;
use App\Helpers\ResponseHelper;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $paginator = Project::query()->paginate(
            (int) $request->input('limit', 10)
        );

        return ResponseHelper::paginate($paginator, ProjectResource::class);
    }

    public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = Project::query()->create($request->validated());

        return ResponseHelper::response(new ProjectResource($project), HttpCode::CREATED,);
    }

    public function update(UpdateProjectRequest $request, Project $project): JsonResponse
    {
        $project->update($request->validated());

        return ResponseHelper::response(new ProjectResource($project), HttpCode::ACCEPTED);
    }

    public function destroy(Project $project): JsonResponse
    {
        $project->delete();

        return ResponseHelper::response([
            'message' => 'Project deleted successfully.'
        ], HttpCode::NO_CONTENT);
    }
}
