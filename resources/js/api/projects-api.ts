import { apiClient } from './http';

export interface Project {
    id: number;
    owner_id: number;
    name: string;
    description: string | null;
    created_at: string;
    updated_at: string;
}

interface ResourceResponse<T> {
    data: T;
}

interface CollectionResponse<T> {
    data: T[];
}

export async function listProjects(): Promise<Project[]> {
    return (await apiClient.get<CollectionResponse<Project>>('/projects')).data.data;
}

export async function getProject(projectId: number): Promise<Project> {
    return (await apiClient.get<ResourceResponse<Project>>(`/projects/${projectId}`)).data.data;
}

export async function createProject(payload: Pick<Project, 'name' | 'description'>): Promise<Project> {
    return (await apiClient.post<ResourceResponse<Project>>('/projects', payload)).data.data;
}
