import { apiClient } from './http';

export type ProjectRole = 'owner' | 'member';

export interface ProjectMember {
    id: number;
    name: string;
    email: string;
    role: ProjectRole;
}

interface ResourceResponse<T> {
    data: T;
}

interface CollectionResponse<T> {
    data: T[];
}

export async function listProjectMembers(projectId: number): Promise<ProjectMember[]> {
    return (await apiClient.get<CollectionResponse<ProjectMember>>(`/projects/${projectId}/members`)).data.data;
}

export async function addProjectMember(projectId: number, email: string): Promise<ProjectMember> {
    return (await apiClient.post<ResourceResponse<ProjectMember>>(`/projects/${projectId}/members`, { email })).data.data;
}

export async function removeProjectMember(projectId: number, userId: number): Promise<void> {
    await apiClient.delete(`/projects/${projectId}/members/${userId}`);
}
